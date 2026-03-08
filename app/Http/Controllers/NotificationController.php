<?php

namespace App\Http\Controllers;

use App\Enums\UserRoleEnums;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $titlePage = 'Notifications';
        $notifications = $user->notifications()->latest()->paginate(20);
        $unreadCount = $user->unreadNotifications()->count();

        if ($user->role->value === UserRoleEnums::ADMIN->value) {
            return view('admin.notifications.index', compact('titlePage', 'notifications', 'unreadCount'));
        }

        return view('notifications.index', compact('titlePage', 'notifications', 'unreadCount'));
    }

    public function markRead(Request $request, string $notificationId): RedirectResponse
    {
        $notification = $request->user()->notifications()->where('id', $notificationId)->firstOrFail();
        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        return redirect()->to($this->safeRedirect($request));
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return redirect()->to($this->safeRedirect($request));
    }

    public function open(Request $request, string $notificationId): RedirectResponse
    {
        $notification = $request->user()->notifications()->where('id', $notificationId)->firstOrFail();
        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        $actionUrl = data_get($notification->data, 'action_url');
        if (! is_string($actionUrl) || trim($actionUrl) === '' || ! $this->isSafeInternalUrl($actionUrl, $request)) {
            return redirect()->route('notifications.index');
        }

        return redirect()->to($actionUrl);
    }

    private function safeRedirect(Request $request): string
    {
        $fallback = route('notifications.index');
        $target = $request->input('redirect_to');

        if (! is_string($target) || trim($target) === '') {
            return $fallback;
        }

        if (str_starts_with($target, '/')) {
            return $target;
        }

        $appUrl = rtrim((string) config('app.url'), '/');
        if ($appUrl !== '' && str_starts_with($target, $appUrl)) {
            return $target;
        }

        if ($this->isSafeInternalUrl($target, $request)) {
            return $target;
        }

        return $fallback;
    }

    private function isSafeInternalUrl(string $url, Request $request): bool
    {
        if (str_starts_with($url, '/')) {
            return true;
        }

        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $targetHost = parse_url($url, PHP_URL_HOST);
        if (! is_string($targetHost)) {
            return false;
        }

        return strcasecmp($targetHost, $request->getHost()) === 0;
    }
}
