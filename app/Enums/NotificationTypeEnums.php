<?php

namespace App\Enums;

enum NotificationTypeEnums: string
{
    case FORUM_NEW_COMMENT = 'forum_new_comment';
    case FORUM_REPLY = 'forum_reply';
    case COURSE_CONTRIBUTOR_SHARED = 'course_contributor_shared';
    case COURSE_CONTRIBUTOR_REVOKED = 'course_contributor_revoked';
    case ADMIN_BROADCAST = 'admin_broadcast';
}
