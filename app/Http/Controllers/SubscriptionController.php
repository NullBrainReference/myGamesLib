<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $activeSubscription = $user->activeSubscription;

        return view('subscriptions.index', compact('user', 'activeSubscription'));
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:plus,enterprise',
        ]);

        $user = auth()->user();
        $plan = $request->input('plan');

        // Cancel existing active subscriptions
        $user->subscriptions()->where('status', 'active')->update(['status' => 'canceled']);

        // Create new active subscription valid for 30 days
        Subscription::create([
            'user_id'   => $user->id,
            'plan_name' => $plan,
            'status'    => 'active',
            'starts_at' => now(),
            'ends_at'   => now()->addDays(30),
        ]);

        $planLabel = ucfirst($plan);
        return redirect()->route('subscriptions.index')
            ->with('success', "Congratulations! You are now subscribed to the {$planLabel} Plan.");
    }

    public function cancel()
    {
        $user = auth()->user();
        $user->subscriptions()->where('status', 'active')->update(['status' => 'canceled']);

        return back()->with('success', 'Your subscription has been canceled.');
    }
}