<?php
namespace App\Enums;

enum CoursePaymentTypeEnums: string
{
    case CARD = 'card';
    case POINT = 'point';
    case FREE = 'free';

    public static function getValues(): array {
        return [
            self::CARD,
            self::POINT,
            self::FREE,
        ];
    }
}
