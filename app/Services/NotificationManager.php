<?php

namespace App\Services;

use App\Enums\NotificationTypeEnums;
use App\Models\Comment;
use App\Models\Course;
use App\Models\Forum;
use App\Models\NotificationRule;
use App\Models\User;
use App\Notifications\CentralizedNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class NotificationManager
{
    /**
     * @param  User|Collection<int, User>|array<int, User>  $recipients
     * @param  array<string, mixed>  $payload
     * @param  array<int, string>|null  $channels
     */
    public function send(User|Collection|array $recipients, NotificationTypeEnums $type, array $payload, ?array $channels = null): void
    {
        $recipientCollection = $this->normalizeRecipients($recipients);
        if ($recipientCollection->isEmpty()) {
            return;
        }

        $rule = null;
        if (Schema::hasTable('notification_rules')) {
            $rule = NotificationRule::query()->where('event_key', $type->value)->first();
        }
        if ($rule && ! $rule->is_enabled) {
            return;
        }

        $resolvedChannels = $this->resolveChannels($type, $rule, $channels);
        if (empty($resolvedChannels)) {
            return;
        }

        $notificationPayload = $this->buildPayload($type, $payload, $rule);

        $notification = new CentralizedNotification($notificationPayload, $resolvedChannels);
        $recipientCollection->each(fn (User $user) => $user->notify($notification));
    }

    public function notifyForumNewComment(User $recipient, User $actor, Forum $forum, Comment $comment): void
    {
        $this->send(
            $recipient,
            NotificationTypeEnums::FORUM_NEW_COMMENT,
            [
                'title' => 'New comment on your post',
                'subject' => 'New forum comment [Happy Learn]',
                'greeting' => 'Hello, ' . $recipient->name,
                'line' => $actor->name . ' commented on your discussion: "' . $this->limit($forum->text) . '"',
                'action_text' => 'View Discussion',
                'action_url' => route('forums', $forum->lesson_id),
                'end' => 'Stay engaged with your learners.',
                'forum_id' => $forum->id,
                'comment_id' => $comment->id,
                'actor_id' => $actor->id,
                'actor_name' => $actor->name,
                'recipient_name' => $recipient->name,
                'forum_text' => $this->limit($forum->text),
                'comment_text' => $this->limit($comment->text),
            ]
        );
    }

    public function notifyForumReply(User $recipient, User $actor, Forum $forum, Comment $comment): void
    {
        $this->send(
            $recipient,
            NotificationTypeEnums::FORUM_REPLY,
            [
                'title' => 'New reply to your comment',
                'subject' => 'New forum reply [Happy Learn]',
                'greeting' => 'Hello, ' . $recipient->name,
                'line' => $actor->name . ' replied in the lesson discussion: "' . $this->limit($comment->text) . '"',
                'action_text' => 'Open Thread',
                'action_url' => route('forums', $forum->lesson_id),
                'end' => 'Keep the discussion moving.',
                'forum_id' => $forum->id,
                'comment_id' => $comment->id,
                'actor_id' => $actor->id,
                'actor_name' => $actor->name,
                'recipient_name' => $recipient->name,
                'forum_text' => $this->limit($forum->text),
                'comment_text' => $this->limit($comment->text),
            ]
        );
    }

    public function notifyContributorShared(User $recipient, User $actor, Course $course): void
    {
        $this->send(
            $recipient,
            NotificationTypeEnums::COURSE_CONTRIBUTOR_SHARED,
            [
                'title' => 'Contributor access granted',
                'subject' => 'Contributor Permission Access [Happy Learn]',
                'greeting' => 'Hello, ' . $recipient->name,
                'line' => $actor->name . ' gave you contributor access to "' . $course->title . '".',
                'action_text' => 'Check Now',
                'action_url' => route('course.index'),
                'end' => 'Check out your new responsibility.',
                'course_id' => $course->id,
                'actor_id' => $actor->id,
                'actor_name' => $actor->name,
                'recipient_name' => $recipient->name,
                'course_title' => $course->title,
            ]
        );
    }

    public function notifyContributorRevoked(User $recipient, User $actor, Course $course): void
    {
        $this->send(
            $recipient,
            NotificationTypeEnums::COURSE_CONTRIBUTOR_REVOKED,
            [
                'title' => 'Contributor access revoked',
                'subject' => 'Contributor Permission Revoked [Happy Learn]',
                'greeting' => 'Hello, ' . $recipient->name,
                'line' => $actor->name . ' revoked your contributor access for "' . $course->title . '".',
                'action_text' => 'View Courses',
                'action_url' => route('course.index'),
                'end' => 'If this is unexpected, contact an admin.',
                'course_id' => $course->id,
                'actor_id' => $actor->id,
                'actor_name' => $actor->name,
                'recipient_name' => $recipient->name,
                'course_title' => $course->title,
            ]
        );
    }

    /**
     * @return array<int, string>
     */
    private function defaultChannels(NotificationTypeEnums $type): array
    {
        $configChannels = config('notification_triggers.' . $type->value . '.channels', []);
        if (is_array($configChannels) && ! empty($configChannels)) {
            return array_values(array_unique(array_filter($configChannels, fn ($channel) => is_string($channel) && $channel !== '')));
        }

        return match ($type) {
            NotificationTypeEnums::FORUM_NEW_COMMENT,
            NotificationTypeEnums::FORUM_REPLY => ['database'],
            NotificationTypeEnums::COURSE_CONTRIBUTOR_SHARED,
            NotificationTypeEnums::COURSE_CONTRIBUTOR_REVOKED => ['mail', 'database'],
            NotificationTypeEnums::ADMIN_BROADCAST => ['database'],
        };
    }

    /**
     * @param  array<int, string>|null  $channels
     * @return array<int, string>
     */
    private function resolveChannels(NotificationTypeEnums $type, ?NotificationRule $rule, ?array $channels): array
    {
        if (is_array($channels) && ! empty($channels)) {
            return array_values(array_unique(array_filter($channels, fn ($channel) => is_string($channel) && $channel !== '')));
        }

        if ($rule && is_array($rule->channels) && ! empty($rule->channels)) {
            return array_values(array_unique(array_filter($rule->channels, fn ($channel) => is_string($channel) && $channel !== '')));
        }

        return $this->defaultChannels($type);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function buildPayload(NotificationTypeEnums $type, array $payload, ?NotificationRule $rule): array
    {
        $defaultTemplate = config('notification_triggers.' . $type->value . '.templates', []);
        if (! is_array($defaultTemplate)) {
            $defaultTemplate = [];
        }

        $notificationPayload = array_merge(
            $defaultTemplate,
            [
                'type' => $type->value,
                'created_at' => now()->toDateTimeString(),
            ],
            $payload
        );

        if (! $rule) {
            return $notificationPayload;
        }

        $templateMap = [
            'title' => $rule->template_title,
            'subject' => $rule->template_subject,
            'line' => $rule->template_line,
            'action_text' => $rule->template_action_text,
            'end' => $rule->template_end,
        ];

        foreach ($templateMap as $key => $templateValue) {
            if (is_string($templateValue) && trim($templateValue) !== '') {
                $notificationPayload[$key] = $this->interpolateTemplate($templateValue, $notificationPayload);
            }
        }

        return $notificationPayload;
    }

    /**
     * @param  User|Collection<int, User>|array<int, User>  $recipients
     * @return Collection<int, User>
     */
    private function normalizeRecipients(User|Collection|array $recipients): Collection
    {
        $collection = $recipients instanceof User
            ? collect([$recipients])
            : collect($recipients);

        return $collection
            ->filter(fn ($recipient) => $recipient instanceof User)
            ->unique(fn (User $user) => $user->id)
            ->values();
    }

    private function limit(string $text, int $max = 85): string
    {
        if (mb_strlen($text) <= $max) {
            return $text;
        }

        return mb_substr($text, 0, $max - 3) . '...';
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function interpolateTemplate(string $template, array $payload): string
    {
        return (string) preg_replace_callback('/\{([a-zA-Z0-9_]+)\}/', function (array $match) use ($payload) {
            $key = $match[1] ?? '';
            $value = $payload[$key] ?? null;

            if (is_scalar($value)) {
                return (string) $value;
            }

            return $match[0];
        }, $template);
    }
}
