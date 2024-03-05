<?php

namespace App\Enums;
enum UserRoleEnums: string
{
    case TEACHER = 'teacher';
    case STUDENT = 'student';
    case ADMIN = 'admin';

    public static function getValues(): array {
        return [
            self::ADMIN,
            self::TEACHER,
            self::STUDENT,
        ];
    }
}