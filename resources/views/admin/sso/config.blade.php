@extends('admin.layouts.app')

@section('content')
    @if(Session::has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ Session::pull('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(Session::has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ Session::pull('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card mb-3">
        <div class="card-body pt-3">
            <h5 class="card-title mb-1">SSO Provider Security</h5>
            <p class="text-muted mb-2">
                Client secrets are encrypted at rest. Existing secrets are never shown in plain text.
                Leave secret blank during update if you do not want to rotate it.
            </p>
            <p class="text-muted mb-0">
                Callback pattern: <code>{{ url('/login/sso/{provider_key}/callback') }}</code>
            </p>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body pt-3">
            <h5 class="card-title mb-1">Create New SSO Provider</h5>
            <p class="text-muted mb-3">Add and configure a new provider (for example: facebook).</p>
            <form action="{{ route('admin.sso.providers.store') }}" method="POST" class="row g-3">
                @csrf
                <div class="col-md-3">
                    <label class="form-label">Provider Key</label>
                    <input type="text" name="provider_key" class="form-control" placeholder="facebook" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Display Name</label>
                    <input type="text" name="display_name" class="form-control" placeholder="Facebook" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Driver</label>
                    <select name="driver" class="form-select" required>
                        @foreach($supportedDrivers as $driverKey => $driverMeta)
                            <option value="{{ $driverKey }}">{{ $driverMeta['label'] }} ({{ $driverKey }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Icon Class</label>
                    <input type="text" name="icon_class" class="form-control" placeholder="bi-facebook">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Client ID</label>
                    <input type="text" name="client_id" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Client Secret</label>
                    <input type="password" name="client_secret" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tenant (Microsoft only)</label>
                    <input type="text" name="tenant" class="form-control" placeholder="common">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Custom Redirect URI (Optional)</label>
                    <input type="url" name="redirect_uri" class="form-control" placeholder="{{ url('/login/sso/facebook/callback') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Scopes (comma-separated)</label>
                    <input type="text" name="scopes" class="form-control" placeholder="email,public_profile">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Sort Order</label>
                    <input type="number" name="sort_order" class="form-control" value="0" min="0" max="999">
                </div>

                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_enabled" value="1" id="createEnabled" checked>
                        <label class="form-check-label" for="createEnabled">Enable provider immediately</label>
                    </div>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Create Provider</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body pt-3">
            <h5 class="card-title mb-1">Configured Providers</h5>
            <p class="text-muted mb-3">Update provider settings and rotate secrets safely.</p>

            <div class="accordion" id="ssoProviderAccordion">
                @forelse($providers as $provider)
                    @php
                        $scopesText = is_array($provider->scopes) ? implode(',', $provider->scopes) : '';
                    @endphp
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingProvider{{ $provider->id }}">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseProvider{{ $provider->id }}" aria-expanded="false" aria-controls="collapseProvider{{ $provider->id }}">
                                {{ $provider->display_name }}
                                <span class="ms-2 text-muted">({{ $provider->provider_key }} / {{ $provider->driver }})</span>
                                @if($provider->is_enabled)
                                    <span class="badge bg-success ms-2">Enabled</span>
                                @else
                                    <span class="badge bg-secondary ms-2">Disabled</span>
                                @endif
                            </button>
                        </h2>
                        <div id="collapseProvider{{ $provider->id }}" class="accordion-collapse collapse" aria-labelledby="headingProvider{{ $provider->id }}" data-bs-parent="#ssoProviderAccordion">
                            <div class="accordion-body">
                                <form action="{{ route('admin.sso.providers.update', $provider) }}" method="POST" class="row g-3 mb-3">
                                    @csrf
                                    @method('PUT')

                                    <div class="col-md-3">
                                        <label class="form-label">Provider Key</label>
                                        <input type="text" name="provider_key" class="form-control" value="{{ $provider->provider_key }}" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Display Name</label>
                                        <input type="text" name="display_name" class="form-control" value="{{ $provider->display_name }}" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Driver</label>
                                        <select name="driver" class="form-select" required>
                                            @foreach($supportedDrivers as $driverKey => $driverMeta)
                                                <option value="{{ $driverKey }}" {{ $provider->driver === $driverKey ? 'selected' : '' }}>
                                                    {{ $driverMeta['label'] }} ({{ $driverKey }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Icon Class</label>
                                        <input type="text" name="icon_class" class="form-control" value="{{ $provider->icon_class }}">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Client ID</label>
                                        <input type="text" name="client_id" class="form-control" value="{{ $provider->client_id }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Client Secret (Rotate)</label>
                                        <input type="password" name="client_secret" class="form-control" placeholder="Leave blank to keep existing secret">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Tenant (Microsoft only)</label>
                                        <input type="text" name="tenant" class="form-control" value="{{ $provider->tenant }}">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Custom Redirect URI (Optional)</label>
                                        <input type="url" name="redirect_uri" class="form-control" value="{{ $provider->redirect_uri }}">
                                        <small class="text-muted">Default: {{ route('login.sso.callback', ['providerKey' => $provider->provider_key]) }}</small>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Scopes (comma-separated)</label>
                                        <input type="text" name="scopes" class="form-control" value="{{ $scopesText }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Sort Order</label>
                                        <input type="number" name="sort_order" class="form-control" value="{{ $provider->sort_order }}" min="0" max="999">
                                    </div>

                                    <div class="col-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="is_enabled" value="1" id="enabledProvider{{ $provider->id }}" {{ $provider->is_enabled ? 'checked' : '' }}>
                                            <label class="form-check-label" for="enabledProvider{{ $provider->id }}">Enable this provider</label>
                                        </div>
                                    </div>

                                    <div class="col-12 d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">Save Provider</button>
                                        <a href="{{ route('login.sso.redirect', ['providerKey' => $provider->provider_key]) }}" class="btn btn-outline-secondary" target="_blank">Test Redirect</a>
                                    </div>
                                </form>

                                <form action="{{ route('admin.sso.providers.destroy', $provider) }}" method="POST" onsubmit="return confirm('Delete provider {{ $provider->provider_key }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">Delete Provider</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-muted">No providers configured yet.</div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
