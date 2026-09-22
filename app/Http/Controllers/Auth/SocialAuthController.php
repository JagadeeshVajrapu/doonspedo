<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Exception;

class SocialAuthController extends Controller
{
    /**
     * Redirect the user to the social provider authentication page.
     *
     * @param string $provider
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from the social provider.
     *
     * @param string $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (Exception $e) {
            return redirect('/login')->with('error', 'Authentication failed for ' . ucfirst($provider));
        }

        // Search for the user by email
        $user = User::where('email', $socialUser->getEmail())->first();

        if (!$user) {
            // Create a new user if they don't exist
            $user = User::create([
                'name'          => $socialUser->getName() ?? $socialUser->getNickname() ?? 'Social User',
                'email'         => $socialUser->getEmail(),
                'provider_id'   => $socialUser->getId(),
                'provider_name' => $provider,
                'password'      => bcrypt(Str::random(24)), // Random password for security
            ]);
        } else {
            // If the user exists, update their provider information if not already set
            if (!$user->provider_id) {
                $user->update([
                    'provider_id'   => $socialUser->getId(),
                    'provider_name' => $provider,
                ]);
            }
        }

        // Log the user in
        Auth::login($user);

        // Redirect to a specific path after successful login
        return redirect('/')->with('success', 'You have successfully logged in via ' . ucfirst($provider));
    }
}
