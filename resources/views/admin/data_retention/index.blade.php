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

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Please fix the highlighted validation errors.</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card mb-3">
        <div class="card-body pt-3">
            <h5 class="card-title mb-1">Data Retention Policies</h5>
            <p class="text-muted mb-0">
                Policy flow: archive first, then purge archived snapshots after grace period.
                Notifications are configured to keep unread entries by default.
            </p>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body pt-3">
            <h5 class="card-title mb-3">Retention Configuration</h5>

            <form action="{{ route('admin.data-retention.policies.update') }}" method="POST" class="row g-3">
                @csrf
                @method('PUT')

                @foreach($targets as $targetKey => $meta)
                    @php
                        $policy = $policies->get($targetKey);
                        $keepDays = (int) old("policies.$targetKey.keep_days", $policy?->keep_days ?? $meta['default_keep_days']);
                        $archiveGraceDays = (int) old("policies.$targetKey.archive_grace_days", $policy?->archive_grace_days ?? $meta['default_archive_grace_days']);
                        $isEnabled = (bool) old("policies.$targetKey.is_enabled", $policy?->is_enabled ?? true);
                        $excludeUnread = (bool) old("policies.$targetKey.exclude_unread_notifications", $policy?->exclude_unread_notifications ?? true);
                    @endphp
                    <div class="col-12">
                        <div class="border rounded p-3">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                <div>
                                    <h6 class="mb-1">{{ $meta['label'] }} <code class="small">{{ $targetKey }}</code></h6>
                                    <p class="text-muted mb-0">{{ $meta['description'] }}</p>
                                </div>
                                <div class="form-check form-switch">
                                    <input type="hidden" name="policies[{{ $targetKey }}][is_enabled]" value="0">
                                    <input class="form-check-input" type="checkbox" name="policies[{{ $targetKey }}][is_enabled]" value="1" id="enabled_{{ $targetKey }}" {{ $isEnabled ? 'checked' : '' }}>
                                    <label class="form-check-label" for="enabled_{{ $targetKey }}">Enabled</label>
                                </div>
                            </div>

                            <div class="row g-3 mt-1">
                                <div class="col-md-4">
                                    <label class="form-label">Keep Days</label>
                                    <input type="number" name="policies[{{ $targetKey }}][keep_days]" min="1" max="3650" class="form-control" value="{{ $keepDays }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Archive Grace Days</label>
                                    <input type="number" name="policies[{{ $targetKey }}][archive_grace_days]" min="1" max="3650" class="form-control" value="{{ $archiveGraceDays }}" required>
                                </div>

                                @if($meta['supports_unread_exclusion'])
                                    <div class="col-md-4">
                                        <label class="form-label d-block">Unread Notification Handling</label>
                                        <input type="hidden" name="policies[{{ $targetKey }}][exclude_unread_notifications]" value="0">
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" name="policies[{{ $targetKey }}][exclude_unread_notifications]" value="1" id="excludeUnread_{{ $targetKey }}" {{ $excludeUnread ? 'checked' : '' }}>
                                            <label class="form-check-label" for="excludeUnread_{{ $targetKey }}">
                                                Exclude unread notifications from purge
                                            </label>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Save Retention Policies</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body pt-3">
            <h5 class="card-title mb-1">Run Purge Jobs</h5>
            <p class="text-muted mb-3">Dry-run previews impacted rows. Manual run performs archive and purge immediately.</p>

            <div class="row g-3">
                <div class="col-md-6">
                    <form action="{{ route('admin.data-retention.dry-run') }}" method="POST" class="row g-3 border rounded p-3">
                        @csrf
                        <div class="col-12">
                            <label class="form-label">Target</label>
                            <select name="target" class="form-select">
                                <option value="all">All Targets</option>
                                @foreach($targets as $targetKey => $meta)
                                    <option value="{{ $targetKey }}">{{ $meta['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-outline-primary w-100">Run Dry-Run</button>
                        </div>
                    </form>
                </div>

                <div class="col-md-6">
                    <form action="{{ route('admin.data-retention.run') }}" method="POST" class="row g-3 border rounded p-3">
                        @csrf
                        <div class="col-12">
                            <label class="form-label">Target</label>
                            <select name="target" class="form-select">
                                <option value="all">All Targets</option>
                                @foreach($targets as $targetKey => $meta)
                                    <option value="{{ $targetKey }}">{{ $meta['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Run manual purge now?')">Run Manual Purge</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body pt-3">
            <h5 class="card-title mb-1">Recent Purge Runs</h5>
            <p class="text-muted mb-3">Execution logs for dry-run, manual, and scheduled jobs.</p>

            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Run Type</th>
                        <th>Status</th>
                        <th>Eligible</th>
                        <th>Archived</th>
                        <th>Deleted</th>
                        <th>Started</th>
                        <th>Finished</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($runs as $run)
                        @php
                            $totals = $run->summary['totals'] ?? [];
                        @endphp
                        <tr>
                            <td>{{ $run->id }}</td>
                            <td><code>{{ $run->run_type }}</code></td>
                            <td>
                                @if($run->status === 'completed')
                                    <span class="badge bg-success">completed</span>
                                @elseif($run->status === 'failed')
                                    <span class="badge bg-danger">failed</span>
                                @else
                                    <span class="badge bg-secondary">{{ $run->status }}</span>
                                @endif
                            </td>
                            <td>{{ (int) ($totals['eligible_source_rows'] ?? 0) }}</td>
                            <td>{{ (int) ($totals['archived_rows'] ?? 0) }}</td>
                            <td>{{ (int) ($totals['deleted_source_rows'] ?? 0) }}</td>
                            <td>{{ optional($run->started_at)->format('Y-m-d H:i:s') ?? '-' }}</td>
                            <td>{{ optional($run->finished_at)->format('Y-m-d H:i:s') ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No purge runs yet.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
