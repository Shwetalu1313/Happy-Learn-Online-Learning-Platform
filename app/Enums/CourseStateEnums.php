<?php
namespace App\Enums;

enum CourseStateEnums: string
{
    case PENDING = 'p';
    case APPROVED = 'a';

    public static function getValues(): array {
        return [
            self::APPROVED,
            self::PENDING,
        ];
    }
}
