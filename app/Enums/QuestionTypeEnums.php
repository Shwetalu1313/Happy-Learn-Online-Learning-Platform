<?php

namespace App\Enums;

enum QuestionTypeEnums: string
{
    case BLANK = 'blank';
    case TRUEorFALSE = 'true/false';
    case MULTIPLE_CHOICE = 'multiple_choice';

    case TRUE = 'true';
    case FALSE = 'false';

    /**
     * @return array
     */
    public static function getValue(): array {
        return [
            self::BLANK,
            self::TRUEorFALSE,
            self::MULTIPLE_CHOICE,
        ];
    }

}
