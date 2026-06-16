<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use GuzzleHttp\Client;

class SocialiteController extends Controller
{
    /**
     * Handshake Step 1: Forward the client out to the external API cluster.
     */
    public function redirectToProvider()
    {
        return Socialite::driver('github')->redirect();
    }

    /**
     * Handshake Step 2: Ingest the response payload stream from the service.
     */
    public function handleProviderCallback()
    {
        try {
            // 💡 Added: ->setHttpClient() to disable local SSL validation
            // 💡 Added: ->stateless() to prevent local session state mismatches
            $githubUser = Socialite::driver('github')
                ->setHttpClient(new Client(['verify' => false])) // 💡 Cleaner instantiation now
                ->stateless()
                ->user();

        } catch (\Exception $e) {
            // 💡 Temporary: If it fails again, this will print the actual error on screen instead of redirecting
            dd($e->getMessage());

            // return redirect()->route('login')->with('error', 'GitHub Authentication Handshake Failed.');
        }

        // Integrate with Breeze's DB table: Find existing user or generate a new footprint
        $user = User::firstOrCreate([
            'email' => $githubUser->getEmail(),
        ], [
            'name'              => $githubUser->getName() ?? $githubUser->getNickname() ?? 'GitHub User',
            'password'          => bcrypt(Str::random(24)), // 💡 Cleaned up global namespace prefix
            'email_verified_at' => now(),
        ]);

        // Boot up standard Breeze authentication session state structures
        Auth::login($user, true);

        // Redirect safely into your protected project view space
        return redirect()->intended(route('projects', absolute: false))
                         ->with('success', 'Authenticated via SOA Identity API Successfully!');
    }
}
