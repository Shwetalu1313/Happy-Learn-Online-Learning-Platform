<?php

namespace App\Http\Controllers\Auth;

use App\Auth\Socialite\MicrosoftProvider;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleLoginController extends Controller
{
    /**
     * @var array<int, string>
     */
    private array $supportedProviders = ['google', 'github', 'microsoft'];

    public function redirectToProvider(string $provider): RedirectResponse
    {
        $this->assertProviderIsSupported($provider);

        if ($provider === 'microsoft') {
            return Socialite::buildProvider(MicrosoftProvider::class, config('services.microsoft'))
                ->scopes(['openid', 'profile', 'email', 'User.Read'])
                ->redirect();
        }

        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback(string $provider): RedirectResponse
    {
        $this->assertProviderIsSupported($provider);

        try {
            $socialUser = $provider === 'microsoft'
                ? Socialite::buildProvider(MicrosoftProvider::class, config('services.microsoft'))
                    ->scopes(['openid', 'profile', 'email', 'User.Read'])
                    ->user()
                : Socialite::driver($provider)->user();
        } catch (\Throwable $e) {
            report($e);

            return redirect()->route('login')->withErrors([
                'email' => 'Unable to authenticate with '.ucfirst($provider).'. Please try again.',
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
                $existingUser->oauth_provider = $provider;
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
            'oauth_provider' => $provider,
            'oauth_provider_id' => (string) $socialUser->getId(),
        ]);

        Auth::login($newUser, true);

        return redirect()->intended(route('home'));
    }

    private function assertProviderIsSupported(string $provider): void
    {
        abort_unless(in_array($provider, $this->supportedProviders, true), 404);
    }
}
