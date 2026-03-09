<?php

namespace App\Http\Controllers;

use App\Models\SsoProvider;
use App\Services\SsoProviderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminSsoProviderController extends Controller
{
    public function index(SsoProviderService $ssoProviderService): View
    {
        $ssoProviderService->syncDefaultProvidersFromConfig();

        $titlePage = 'SSO Configuration';
        $providers = SsoProvider::query()->orderBy('sort_order')->orderBy('display_name')->get();
        $supportedDrivers = $ssoProviderService->supportedDrivers();

        return view('admin.sso.config', compact('titlePage', 'providers', 'supportedDrivers'));
    }

    public function store(Request $request, SsoProviderService $ssoProviderService): RedirectResponse
    {
        $validated = $this->validateProvider($request, $ssoProviderService);
        $providerKey = Str::lower($validated['provider_key']);

        if (($validated['is_enabled'] ?? false) && (empty($validated['client_id']) || empty($validated['client_secret']))) {
            return redirect()->back()->with('error', 'Enabled providers must have both client ID and client secret.');
        }

        SsoProvider::query()->create([
            'provider_key' => $providerKey,
            'display_name' => $validated['display_name'],
            'driver' => $validated['driver'],
            'client_id' => $validated['client_id'] ?? null,
            'client_secret' => $validated['client_secret'] ?? null,
            'redirect_uri' => $validated['redirect_uri'] ?? null,
            'scopes' => $this->normalizeScopes($validated['scopes'] ?? null),
            'tenant' => $validated['tenant'] ?? null,
            'icon_class' => $validated['icon_class'] ?? null,
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_enabled' => (bool) ($validated['is_enabled'] ?? false),
        ]);

        return redirect()->back()->with('success', 'SSO provider created.');
    }

    public function update(Request $request, SsoProvider $provider, SsoProviderService $ssoProviderService): RedirectResponse
    {
        $validated = $this->validateProvider($request, $ssoProviderService, $provider->id);
        $providerKey = Str::lower($validated['provider_key']);
        $currentSecret = $provider->client_secret;
        $rotatedSecret = $validated['client_secret'] ?? null;
        $secretForValidation = $rotatedSecret ?: $currentSecret;

        if (($validated['is_enabled'] ?? false) && (empty($validated['client_id']) || empty($secretForValidation))) {
            return redirect()->back()->with('error', 'Enabled providers must have both client ID and client secret.');
        }

        $provider->provider_key = $providerKey;
        $provider->display_name = $validated['display_name'];
        $provider->driver = $validated['driver'];
        $provider->client_id = $validated['client_id'] ?? null;
        $provider->redirect_uri = $validated['redirect_uri'] ?? null;
        $provider->scopes = $this->normalizeScopes($validated['scopes'] ?? null);
        $provider->tenant = $validated['tenant'] ?? null;
        $provider->icon_class = $validated['icon_class'] ?? null;
        $provider->sort_order = (int) ($validated['sort_order'] ?? 0);
        $provider->is_enabled = (bool) ($validated['is_enabled'] ?? false);

        if (! empty($rotatedSecret)) {
            $provider->client_secret = $rotatedSecret;
        }

        $provider->save();

        return redirect()->back()->with('success', 'SSO provider updated.');
    }

    public function destroy(SsoProvider $provider): RedirectResponse
    {
        $provider->delete();

        return redirect()->back()->with('success', 'SSO provider deleted.');
    }

    public function testConnection(SsoProvider $provider, SsoProviderService $ssoProviderService): RedirectResponse
    {
        if (! $provider->is_enabled) {
            return redirect()->back()->with('error', "Provider '{$provider->display_name}' is disabled.");
        }

        if (empty($provider->client_id) || empty($provider->client_secret)) {
            return redirect()->back()->with('error', "Provider '{$provider->display_name}' is missing client credentials.");
        }

        try {
            $driver = $ssoProviderService->buildProviderDriver($provider);
            $authUrl = $driver->redirect()->getTargetUrl();
            $host = parse_url($authUrl, PHP_URL_HOST) ?: 'unknown-host';

            $response = Http::timeout(8)
                ->withOptions(['allow_redirects' => false, 'http_errors' => false])
                ->get($authUrl);

            $status = (int) $response->status();
            if ($status >= 200 && $status < 500) {
                return redirect()->back()->with(
                    'success',
                    "Connection test passed for '{$provider->display_name}' (endpoint: {$host}, HTTP {$status})."
                );
            }

            return redirect()->back()->with(
                'error',
                "Connection test failed for '{$provider->display_name}' (endpoint: {$host}, HTTP {$status})."
            );
        } catch (\Throwable $e) {
            report($e);

            return redirect()->back()->with(
                'error',
                "Connection test failed for '{$provider->display_name}'. Please verify credentials and callback URL."
            );
        }
    }

    private function validateProvider(Request $request, SsoProviderService $ssoProviderService, ?int $ignoreId = null): array
    {
        return $request->validate([
            'provider_key' => [
                'required',
                'string',
                'max:50',
                'alpha_dash:ascii',
                Rule::unique('sso_providers', 'provider_key')->ignore($ignoreId),
            ],
            'display_name' => ['required', 'string', 'max:100'],
            'driver' => ['required', Rule::in(array_keys($ssoProviderService->supportedDrivers()))],
            'client_id' => ['nullable', 'string', 'max:255'],
            'client_secret' => ['nullable', 'string', 'max:4000'],
            'redirect_uri' => ['nullable', 'url', 'max:255'],
            'scopes' => ['nullable', 'string', 'max:1000'],
            'tenant' => ['nullable', 'string', 'max:100'],
            'icon_class' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_enabled' => ['nullable', 'boolean'],
        ]);
    }

    /**
     * @return array<int, string>
     */
    private function normalizeScopes(?string $scopeText): array
    {
        if (empty($scopeText)) {
            return [];
        }

        return collect(explode(',', $scopeText))
            ->map(fn ($scope) => trim($scope))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
