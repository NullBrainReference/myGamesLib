<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Client\Payment\PaymentClient;

class SubscriptionController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $activeSubscription = $user->activeSubscription;

        return view('subscriptions.index', compact('user', 'activeSubscription'));
    }

    /**
     * Generate MercadoPago Checkout Preference and redirect user to payment page.
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:plus,enterprise',
        ]);

        $user = auth()->user();
        $plan = $request->input('plan');

        $prices = [
            'plus'       => 1000.00, // ARS test price
            'enterprise' => 1499.00, // ARS test price
        ];

        $planLabels = [
            'plus'       => 'myGamesLib Plus Subscription (1 Month)',
            'enterprise' => 'myGamesLib Enterprise Subscription (1 Month)',
        ];

        try {
            // 1. Verify Access Token is present
            $accessToken = config('services.mercadopago.access_token');
            if (empty($accessToken)) {
                throw new \Exception('MercadoPago Access Token is missing in configuration.');
            }

            MercadoPagoConfig::setAccessToken($accessToken);

            // 2. Build Preference Payload
            $preferenceData = [
                "items" => [
                    [
                        "title"       => $planLabels[$plan],
                        "quantity"    => 1,
                        "unit_price"  => (float) $prices[$plan],
                        "currency_id" => "ARS",
                    ]
                ],
                "payer" => [
                    "email" => $user->email,
                    "name"  => $user->name,
                ],
                "external_reference" => "user_{$user->id}_plan_{$plan}",
                "metadata" => [
                    "user_id"   => $user->id,
                    "plan_name" => $plan,
                ],
                "back_urls" => [
                    "success" => route('subscriptions.mercadopago.success'),
                    "failure" => route('subscriptions.mercadopago.failure'),
                    "pending" => route('subscriptions.mercadopago.pending'),
                ],
            ];

            // Only enable auto_return if using HTTPS (MercadoPago rejects auto_return on http:// / localhost)
            $successUrl = route('subscriptions.mercadopago.success');
            if (str_starts_with($successUrl, 'https://')) {
                $preferenceData["auto_return"] = "approved";
            }

            // Only attach notification_url if not on localhost
            $webhookUrl = route('subscriptions.mercadopago.webhook');
            if (!str_contains($webhookUrl, 'localhost') && !str_contains($webhookUrl, '127.0.0.1')) {
                $preferenceData["notification_url"] = $webhookUrl;
            }

            // 4. Create Preference
            $client = new PreferenceClient();
            $preference = $client->create($preferenceData);

            // 5. Select redirect link (sandbox vs production)
            $checkoutUrl = (config('app.env') === 'local' && !empty($preference->sandbox_init_point))
                ? $preference->sandbox_init_point
                : $preference->init_point;

            return redirect()->away($checkoutUrl);

        } catch (MPApiException $e) {
            // Extract detailed MercadoPago API response
            $response = $e->getApiResponse();
            $details = $response ? json_encode($response->getContent(), JSON_PRETTY_PRINT) : $e->getMessage();
            
            Log::error('MercadoPago API Exception Details: ' . $details);

            $errorMessage = config('app.env') === 'local' 
                ? 'MP API Error: ' . $details 
                : 'Unable to initialize MercadoPago checkout. Please try again.';

            return back()->with('error', $errorMessage);

        } catch (\Exception $e) {
            Log::error('MercadoPago General Error: ' . $e->getMessage());

            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Webhook endpoint to receive real-time payment notifications from MercadoPago.
     */
    public function webhook(Request $request)
    {
        Log::info('MercadoPago Webhook Received:', $request->all());

        $type = $request->input('type') ?? $request->input('topic');
        $dataId = $request->input('data.id') ?? $request->input('id');

        if ($type === 'payment' && $dataId) {
            try {
                MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));

                $paymentClient = new PaymentClient();
                $payment = $paymentClient->get($dataId);

                if ($payment->status === 'approved') {
                    $metadata = $payment->metadata;
                    $userId = $metadata->user_id ?? null;
                    $planName = $metadata->plan_name ?? null;

                    if ($userId && $planName) {
                        // Cancel old active subscriptions
                        Subscription::where('user_id', $userId)
                            ->where('status', 'active')
                            ->update(['status' => 'canceled']);

                        // Activate new 30-day subscription
                        Subscription::create([
                            'user_id'   => $userId,
                            'plan_name' => $planName,
                            'status'    => 'active',
                            'starts_at' => now(),
                            'ends_at'   => now()->addDays(30),
                        ]);

                        Log::info("Subscription activated via MercadoPago for User ID {$userId}, Plan: {$planName}");
                    }
                }
            } catch (\Exception $e) {
                Log::error('MercadoPago Webhook Error: ' . $e->getMessage());
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        return response()->json(['status' => 'ok'], 200);
    }

    public function success(Request $request)
    {
        return redirect()->route('subscriptions.index')
            ->with('success', 'Payment approved! Your subscription has been activated.');
    }

    public function failure()
    {
        return redirect()->route('subscriptions.index')
            ->with('error', 'Payment was declined or canceled. Please try again.');
    }

    public function pending()
    {
        return redirect()->route('subscriptions.index')
            ->with('warning', 'Payment is pending verification. Your subscription will activate once payment confirms.');
    }

    public function cancel()
    {
        $user = auth()->user();
        $user->subscriptions()->where('status', 'active')->update(['status' => 'canceled']);

        return back()->with('success', 'Your subscription has been canceled.');
    }
}