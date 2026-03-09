<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SsoProviderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GoogleLoginController extends Controller
{
    public function redirectToProvider(string $providerKey, SsoProviderService $ssoProviderService): RedirectResponse
    {
        $ssoProviderService->syncDefaultProvidersFromConfig();
        $provider = $ssoProviderService->getProviderByKey($providerKey);
        if (! $provider) {
            return redirect()->route('login')->withErrors([
                'email' => 'SSO provider is not available or disabled.',
            ]);
        }

        try {
            return $ssoProviderService->buildProviderDriver($provider)->redirect();
        } catch (\Throwable $e) {
            report($e);

            return redirect()->route('login')->withErrors([
                'email' => 'Unable to initialize '.$provider->display_name.' sign-in.',
            ]);
        }
    }

    public function handleProviderCallback(string $providerKey, SsoProviderService $ssoProviderService): RedirectResponse
    {
        $ssoProviderService->syncDefaultProvidersFromConfig();
        $provider = $ssoProviderService->getProviderByKey($providerKey);
        if (! $provider) {
            return redirect()->route('login')->withErrors([
                'email' => 'SSO provider is not available or disabled.',
            ]);
        }

        try {
            $socialUser = $ssoProviderService->buildProviderDriver($provider)->user();
        } catch (\Throwable $e) {
            report($e);

            return redirect()->route('login')->withErrors([
                'email' => 'Unable to authenticate with '.$provider->display_name.'. Please try again.',
            ]);
        }

        $email = $socialUser->getEmail();
        if (empty($email)) {
            return redirect()->route('login')->withErrors([
                'email' => ucfirst($provider).' did not return an email address.',
            ]);
        }

        $existingUser = User::where('email', $email)->first();
        if ($existingUser) {
            if (empty($existingUser->oauth_provider) || empty($existingUser->oauth_provider_id)) {
                $existingUser->oauth_provider = $provider->provider_key;
                $existingUser->oauth_provider_id = (string) $socialUser->getId();
                $existingUser->save();
            }

            Auth::login($existingUser, true);

            return redirect()->intended(route('home'));
        }

        $newUser = User::create([
            'name' => $socialUser->getName() ?: Str::before($email, '@'),
            'email' => $email,
            'birthdate' => now()->subYears(18)->toDateString(),
            'password' => Str::random(40),
            'email_verified_at' => now(),
            'oauth_provider' => $provider->provider_key,
            'oauth_provider_id' => (string) $socialUser->getId(),
        ]);

        Auth::login($newUser, true);

        return redirect()->intended(route('home'));
    }
}
