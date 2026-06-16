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
    public function redirectToProvider()
    {
        return Socialite::driver('github')->redirect();
    }

    public function handleProviderCallback()
    {
        try {
            $githubUser = Socialite::driver('github')
                ->setHttpClient(new Client(['verify' => false]))
                ->stateless()
                ->user();

        } catch (\Exception $e) {
            dd($e->getMessage());

        }

        $user = User::firstOrCreate([
            'email' => $githubUser->getEmail(),
        ], [
            'name'              => $githubUser->getName() ?? $githubUser->getNickname() ?? 'GitHub User',
            'password'          => bcrypt(Str::random(24)),
            'email_verified_at' => now(),
        ]);

        Auth::login($user, true);

        return redirect()->intended(route('projects', absolute: false))
                         ->with('success', 'Authenticated via SOA Identity API Successfully!');
    }
}
