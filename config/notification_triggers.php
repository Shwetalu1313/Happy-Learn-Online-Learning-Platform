<?php

return [
    'forum_new_comment' => [
        'label' => 'Forum: New Comment',
        'description' => 'Notify forum owner when a new top-level comment is posted.',
        'channels' => ['database'],
        'templates' => [
            'title' => 'New comment on your post',
            'subject' => 'New forum comment [Happy Learn]',
            'line' => '{actor_name} commented on your discussion.',
            'action_text' => 'View Discussion',
            'end' => 'Stay engaged with your learners.',
        ],
    ],
    'forum_reply' => [
        'label' => 'Forum: Reply',
        'description' => 'Notify comment owner when someone replies.',
        'channels' => ['database'],
        'templates' => [
            'title' => 'New reply to your comment',
            'subject' => 'New forum reply [Happy Learn]',
            'line' => '{actor_name} replied in the lesson discussion.',
            'action_text' => 'Open Thread',
            'end' => 'Keep the discussion moving.',
        ],
    ],
    'course_contributor_shared' => [
        'label' => 'Course: Contributor Shared',
        'description' => 'Notify teacher when contributor access is granted.',
        'channels' => ['mail', 'database'],
        'templates' => [
            'title' => 'Contributor access granted',
            'subject' => 'Contributor Permission Access [Happy Learn]',
            'line' => '{actor_name} gave you contributor access to {course_title}.',
            'action_text' => 'Check Now',
            'end' => 'Check out your new responsibility.',
        ],
    ],
    'course_contributor_revoked' => [
        'label' => 'Course: Contributor Revoked',
        'description' => 'Notify teacher when contributor access is revoked.',
        'channels' => ['mail', 'database'],
        'templates' => [
            'title' => 'Contributor access revoked',
            'subject' => 'Contributor Permission Revoked [Happy Learn]',
            'line' => '{actor_name} revoked your contributor access for {course_title}.',
            'action_text' => 'View Courses',
            'end' => 'If this is unexpected, contact an admin.',
        ],
    ],
    'admin_broadcast' => [
        'label' => 'Admin Broadcast',
        'description' => 'Manual notification triggered by admin.',
        'channels' => ['database'],
        'templates' => [
            'title' => 'Announcement',
            'subject' => 'New Announcement [Happy Learn]',
            'line' => '{line}',
            'action_text' => 'Open',
            'end' => 'Thank you.',
        ],
    ],
];
