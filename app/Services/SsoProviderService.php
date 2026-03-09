<?php

namespace App\Services;

use App\Auth\Socialite\MicrosoftProvider;
use App\Models\SsoProvider;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Schema;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\BitbucketProvider;
use Laravel\Socialite\Two\FacebookProvider;
use Laravel\Socialite\Two\GithubProvider;
use Laravel\Socialite\Two\GitlabProvider;
use Laravel\Socialite\Two\GoogleProvider;
use Laravel\Socialite\Two\ProviderInterface;

class SsoProviderService
{
    /**
     * @return array<string, array{label:string, class:string, icon:string, default_scopes:array<int,string>}>
     */
    public function supportedDrivers(): array
    {
        return [
            'google' => [
                'label' => 'Google',
                'class' => GoogleProvider::class,
                'icon' => 'bi-google',
                'default_scopes' => ['openid', 'profile', 'email'],
            ],
            'github' => [
                'label' => 'GitHub',
                'class' => GithubProvider::class,
                'icon' => 'bi-github',
                'default_scopes' => ['user:email'],
            ],
            'facebook' => [
                'label' => 'Facebook',
                'class' => FacebookProvider::class,
                'icon' => 'bi-facebook',
                'default_scopes' => ['email'],
            ],
            'gitlab' => [
                'label' => 'GitLab',
                'class' => GitlabProvider::class,
                'icon' => 'bi-git',
                'default_scopes' => ['read_user'],
            ],
            'bitbucket' => [
                'label' => 'Bitbucket',
                'class' => BitbucketProvider::class,
                'icon' => 'bi-diagram-3',
                'default_scopes' => ['account', 'email'],
            ],
            'microsoft' => [
                'label' => 'Microsoft',
                'class' => MicrosoftProvider::class,
                'icon' => 'bi-microsoft',
                'default_scopes' => ['openid', 'profile', 'email', 'User.Read'],
            ],
        ];
    }

    public function syncDefaultProvidersFromConfig(): void
    {
        if (! Schema::hasTable('sso_providers')) {
            return;
        }

        $defaults = [
            'google' => [
                'client_id' => config('services.google.client_id'),
                'client_secret' => config('services.google.client_secret'),
                'redirect' => config('services.google.redirect'),
                'tenant' => null,
            ],
            'github' => [
                'client_id' => config('services.github.client_id'),
                'client_secret' => config('services.github.client_secret'),
                'redirect' => config('services.github.redirect'),
                'tenant' => null,
            ],
            'microsoft' => [
                'client_id' => config('services.microsoft.client_id'),
                'client_secret' => config('services.microsoft.client_secret'),
                'redirect' => config('services.microsoft.redirect'),
                'tenant' => config('services.microsoft.tenant', 'common'),
            ],
        ];

        foreach ($defaults as $providerKey => $credentials) {
            $supported = $this->supportedDrivers()[$providerKey] ?? null;
            if (! $supported) {
                continue;
            }

            /** @var SsoProvider $provider */
            $provider = SsoProvider::query()->firstOrNew(['provider_key' => $providerKey]);
            $provider->display_name = $provider->display_name ?: $supported['label'];
            $provider->driver = $provider->driver ?: $providerKey;
            $provider->icon_class = $provider->icon_class ?: $supported['icon'];
            $provider->scopes = $provider->scopes ?: $supported['default_scopes'];
            $provider->tenant = $provider->tenant ?: $credentials['tenant'];
            $provider->redirect_uri = $provider->redirect_uri ?: $credentials['redirect'];

            if (empty($provider->client_id) && ! empty($credentials['client_id'])) {
                $provider->client_id = $credentials['client_id'];
            }

            if (empty($provider->client_secret) && ! empty($credentials['client_secret'])) {
                $provider->client_secret = $credentials['client_secret'];
            }

            if (! $provider->exists) {
                $provider->is_enabled = ! empty($provider->client_id) && ! empty($provider->client_secret);
            }

            $provider->save();
        }
    }

    public function getProviderByKey(string $providerKey): ?SsoProvider
    {
        if (! Schema::hasTable('sso_providers')) {
            return null;
        }

        return SsoProvider::query()
            ->where('provider_key', $providerKey)
            ->where('is_enabled', true)
            ->first();
    }

    /**
     * @return Collection<int, SsoProvider>
     */
    public function getPublicEnabledProviders(): Collection
    {
        if (! Schema::hasTable('sso_providers')) {
            return new Collection();
        }

        $this->syncDefaultProvidersFromConfig();

        return SsoProvider::query()
            ->where('is_enabled', true)
            ->whereNotNull('client_id')
            ->whereNotNull('client_secret')
            ->orderBy('sort_order')
            ->orderBy('display_name')
            ->get();
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function buildProviderDriver(SsoProvider $provider): ProviderInterface
    {
        $supported = $this->supportedDrivers()[$provider->driver] ?? null;
        if (! $supported) {
            throw new \InvalidArgumentException("Unsupported SSO driver: {$provider->driver}");
        }

        $config = [
            'client_id' => $provider->client_id,
            'client_secret' => $provider->client_secret,
            'redirect' => $provider->redirect_uri ?: route('login.sso.callback', ['providerKey' => $provider->provider_key]),
            'tenant' => $provider->tenant ?: 'common',
        ];

        /** @var ProviderInterface $driver */
        $driver = Socialite::buildProvider($supported['class'], $config);

        if (! empty($provider->scopes)) {
            $driver->scopes(array_values($provider->scopes));
        }

        return $driver;
    }
}
