<?php
namespace App\Enums;

enum CourseTypeEnums: string
{
    case BASIC = 'b';
    case ADVANCED = 'a';

    public static function getValues(): array {
        return [
            self::ADVANCED,
            self::BASIC,
        ];
    }
}
