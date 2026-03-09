<?php

namespace App\Auth\Socialite;

use GuzzleHttp\RequestOptions;
use Illuminate\Support\Arr;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

class MicrosoftProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * Requested OAuth scopes.
     *
     * @var array<int, string>
     */
    protected $scopes = ['openid', 'profile', 'email', 'User.Read'];

    /**
     * Microsoft expects a space-separated scopes list.
     */
    protected $scopeSeparator = ' ';

    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase($this->baseUrl().'/authorize', $state);
    }

    protected function getTokenUrl()
    {
        return $this->baseUrl().'/token';
    }

    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get('https://graph.microsoft.com/v1.0/me', [
            RequestOptions::HEADERS => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer '.$token,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id' => Arr::get($user, 'id'),
            'nickname' => Arr::get($user, 'displayName'),
            'name' => Arr::get($user, 'displayName'),
            'email' => Arr::get($user, 'mail') ?: Arr::get($user, 'userPrincipalName'),
            'avatar' => null,
        ]);
    }

    private function baseUrl(): string
    {
        $tenant = config('services.microsoft.tenant', 'common');

        return 'https://login.microsoftonline.com/'.$tenant.'/oauth2/v2.0';
    }
}
