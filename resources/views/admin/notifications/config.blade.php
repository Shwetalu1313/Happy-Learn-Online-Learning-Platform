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
            <h5 class="card-title mb-1">Notification Trigger Rules</h5>
            <p class="text-muted mb-3">Configure channels and templates per trigger. Template placeholders are supported such as <code>{actor_name}</code>, <code>{course_title}</code>, <code>{line}</code>.</p>

            <div class="accordion" id="notificationRulesAccordion">
                @foreach($rules as $rule)
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading{{ $rule->id }}">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $rule->id }}" aria-expanded="false" aria-controls="collapse{{ $rule->id }}">
                                {{ $rule->label }} <span class="ms-2 text-muted">({{ $rule->event_key }})</span>
                            </button>
                        </h2>
                        <div id="collapse{{ $rule->id }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $rule->id }}" data-bs-parent="#notificationRulesAccordion">
                            <div class="accordion-body">
                                <form action="{{ route('admin.notifications.rules.update', $rule->event_key) }}" method="POST" class="row g-3">
                                    @csrf
                                    <div class="col-md-6">
                                        <label class="form-label">Label</label>
                                        <input type="text" name="label" value="{{ $rule->label }}" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Event Key</label>
                                        <input type="text" value="{{ $rule->event_key }}" class="form-control" disabled>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Description</label>
                                        <textarea name="description" class="form-control" rows="2">{{ $rule->description }}</textarea>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-check mt-4">
                                            <input class="form-check-input" type="checkbox" name="is_enabled" value="1" id="enabled{{ $rule->id }}" {{ $rule->is_enabled ? 'checked' : '' }}>
                                            <label class="form-check-label" for="enabled{{ $rule->id }}">Enable trigger</label>
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <label class="form-label">Channels</label>
                                        <div class="d-flex gap-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="channels[]" value="database" id="db{{ $rule->id }}" {{ in_array('database', $rule->channels ?? [], true) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="db{{ $rule->id }}">Database</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="channels[]" value="mail" id="mail{{ $rule->id }}" {{ in_array('mail', $rule->channels ?? [], true) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="mail{{ $rule->id }}">Mail</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Template Title</label>
                                        <input type="text" name="template_title" value="{{ $rule->template_title }}" class="form-control" placeholder="e.g. New comment on your post">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Template Subject (Mail)</label>
                                        <input type="text" name="template_subject" value="{{ $rule->template_subject }}" class="form-control" placeholder="e.g. New forum reply [Happy Learn]">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Template Line</label>
                                        <textarea name="template_line" class="form-control" rows="2" placeholder="e.g. {actor_name} replied to your comment.">{{ $rule->template_line }}</textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Template Action Text</label>
                                        <input type="text" name="template_action_text" value="{{ $rule->template_action_text }}" class="form-control" placeholder="e.g. Open Thread">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Template End</label>
                                        <input type="text" name="template_end" value="{{ $rule->template_end }}" class="form-control" placeholder="e.g. Thank you.">
                                    </div>

                                    <div class="col-12">
                                        <button class="btn btn-primary" type="submit">Save Rule</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body pt-3">
            <h5 class="card-title mb-1">Manual Notification Trigger</h5>
            <p class="text-muted mb-3">Send broadcast notifications directly from admin portal.</p>

            <form action="{{ route('admin.notifications.broadcast') }}" method="POST" class="row g-3" id="broadcastForm">
                @csrf
                <div class="col-md-4">
                    <label class="form-label">Target</label>
                    <select class="form-select" name="target" id="broadcastTarget" required>
                        <option value="all">All Users</option>
                        <option value="admins">Admins</option>
                        <option value="teachers">Teachers</option>
                        <option value="students">Students</option>
                        <option value="single_user">Single User</option>
                    </select>
                </div>
                <div class="col-md-8 d-none" id="singleUserWrap">
                    <label class="form-label">Select User</label>
                    <select class="form-select" name="user_id">
                        <option value="">Choose user</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Channels</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="channels[]" value="database" id="broadcastDatabase" checked>
                            <label class="form-check-label" for="broadcastDatabase">Database</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="channels[]" value="mail" id="broadcastMail">
                            <label class="form-check-label" for="broadcastMail">Mail</label>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" required placeholder="Announcement">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Subject (Mail)</label>
                    <input type="text" name="subject" class="form-control" placeholder="New Announcement [Happy Learn]">
                </div>
                <div class="col-12">
                    <label class="form-label">Message</label>
                    <textarea name="line" class="form-control" rows="3" required placeholder="Write your message..."></textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Action Text</label>
                    <input type="text" name="action_text" class="form-control" placeholder="Open">
                </div>
                <div class="col-md-8">
                    <label class="form-label">Action URL</label>
                    <input type="url" name="action_url" class="form-control" placeholder="https://your-domain/path">
                </div>
                <div class="col-12">
                    <label class="form-label">Footer Text</label>
                    <input type="text" name="end" class="form-control" placeholder="Thank you.">
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Trigger Notification</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const targetSelect = document.getElementById('broadcastTarget');
            const singleUserWrap = document.getElementById('singleUserWrap');

            const toggleSingleUser = () => {
                const isSingleUser = targetSelect.value === 'single_user';
                singleUserWrap.classList.toggle('d-none', !isSingleUser);
            };

            targetSelect.addEventListener('change', toggleSingleUser);
            toggleSingleUser();
        });
    </script>
@endsection
