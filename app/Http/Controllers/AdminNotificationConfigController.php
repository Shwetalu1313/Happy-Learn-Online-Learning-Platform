<?php

namespace App\Http\Controllers;

use App\Enums\NotificationTypeEnums;
use App\Enums\UserRoleEnums;
use App\Models\NotificationRule;
use App\Models\User;
use App\Services\NotificationManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminNotificationConfigController extends Controller
{
    public function index(): View
    {
        if (! Schema::hasTable('notification_rules')) {
            abort(500, 'notification_rules table is missing. Run migrations first.');
        }

        $this->syncDefaultRules();

        $titlePage = 'Notification Configuration';
        $rules = NotificationRule::query()->orderBy('label')->get();
        $users = User::query()->select(['id', 'name', 'email', 'role'])->orderBy('name')->limit(200)->get();

        return view('admin.notifications.config', compact('titlePage', 'rules', 'users'));
    }

    public function updateRule(Request $request, string $eventKey): RedirectResponse
    {
        if (! Schema::hasTable('notification_rules')) {
            return redirect()->back()->with('error', 'notification_rules table is missing. Run migrations first.');
        }

        $this->syncDefaultRules();
        $allowedKeys = collect(NotificationTypeEnums::cases())->map->value->all();

        if (! in_array($eventKey, $allowedKeys, true)) {
            return redirect()->back()->with('error', 'Invalid notification trigger key.');
        }

        $validated = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_enabled' => ['nullable', 'boolean'],
            'channels' => ['nullable', 'array'],
            'channels.*' => [Rule::in(['database', 'mail'])],
            'template_title' => ['nullable', 'string', 'max:255'],
            'template_subject' => ['nullable', 'string', 'max:255'],
            'template_line' => ['nullable', 'string', 'max:2000'],
            'template_action_text' => ['nullable', 'string', 'max:120'],
            'template_end' => ['nullable', 'string', 'max:255'],
        ]);

        NotificationRule::query()->updateOrCreate(
            ['event_key' => $eventKey],
            [
                'label' => $validated['label'],
                'description' => $validated['description'] ?? null,
                'is_enabled' => (bool) ($validated['is_enabled'] ?? false),
                'channels' => array_values($validated['channels'] ?? []),
                'template_title' => $validated['template_title'] ?? null,
                'template_subject' => $validated['template_subject'] ?? null,
                'template_line' => $validated['template_line'] ?? null,
                'template_action_text' => $validated['template_action_text'] ?? null,
                'template_end' => $validated['template_end'] ?? null,
            ]
        );

        return redirect()->back()->with('success', 'Notification rule updated.');
    }

    public function broadcast(Request $request, NotificationManager $notificationManager): RedirectResponse
    {
        $validated = $request->validate([
            'target' => ['required', Rule::in(['all', 'admins', 'teachers', 'students', 'single_user'])],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'channels' => ['nullable', 'array'],
            'channels.*' => [Rule::in(['database', 'mail'])],
            'title' => ['required', 'string', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'line' => ['required', 'string', 'max:2000'],
            'action_text' => ['nullable', 'string', 'max:120'],
            'action_url' => ['nullable', 'url', 'max:255'],
            'end' => ['nullable', 'string', 'max:255'],
        ]);

        $recipients = $this->resolveRecipients($validated);
        if ($recipients->isEmpty()) {
            return redirect()->back()->with('error', 'No recipients found for selected target.');
        }

        $actor = auth()->user();
        $notificationManager->send(
            $recipients,
            NotificationTypeEnums::ADMIN_BROADCAST,
            [
                'title' => $validated['title'],
                'subject' => $validated['subject'] ?? ('Announcement [Happy Learn]'),
                'greeting' => 'Hello,',
                'line' => $validated['line'],
                'action_text' => $validated['action_text'] ?? 'Open',
                'action_url' => $validated['action_url'] ?? route('home'),
                'end' => $validated['end'] ?? 'Thank you.',
                'actor_id' => $actor->id,
                'actor_name' => $actor->name,
            ],
            array_values($validated['channels'] ?? [])
        );

        return redirect()->back()->with('success', 'Broadcast sent to ' . $recipients->count() . ' user(s).');
    }

    private function syncDefaultRules(): void
    {
        if (! Schema::hasTable('notification_rules')) {
            return;
        }

        foreach (NotificationTypeEnums::cases() as $type) {
            $config = config('notification_triggers.' . $type->value, []);
            $templates = $config['templates'] ?? [];

            NotificationRule::query()->firstOrCreate(
                ['event_key' => $type->value],
                [
                    'label' => $config['label'] ?? ucfirst(str_replace('_', ' ', $type->value)),
                    'description' => $config['description'] ?? null,
                    'is_enabled' => true,
                    'channels' => $config['channels'] ?? ['database'],
                    'template_title' => $templates['title'] ?? null,
                    'template_subject' => $templates['subject'] ?? null,
                    'template_line' => $templates['line'] ?? null,
                    'template_action_text' => $templates['action_text'] ?? null,
                    'template_end' => $templates['end'] ?? null,
                ]
            );
        }
    }

    private function resolveRecipients(array $validated)
    {
        $target = $validated['target'];

        return match ($target) {
            'all' => User::query()->select(['id', 'name', 'email', 'role'])->get(),
            'admins' => User::query()->where('role', UserRoleEnums::ADMIN->value)->select(['id', 'name', 'email', 'role'])->get(),
            'teachers' => User::query()->where('role', UserRoleEnums::TEACHER->value)->select(['id', 'name', 'email', 'role'])->get(),
            'students' => User::query()->where('role', UserRoleEnums::STUDENT->value)->select(['id', 'name', 'email', 'role'])->get(),
            'single_user' => User::query()->where('id', $validated['user_id'] ?? 0)->select(['id', 'name', 'email', 'role'])->get(),
            default => collect(),
        };
    }
}
