<?php

namespace App\CustomEntity;

enum Locale: int
{
    case UA = 1;
    case RU = 2;
    case EN = 3;

    static public function getLocaleValue(string $locale): int
    {
        foreach (self::cases() as $val) {
            if ($locale === strtolower($val->name)) {
                return $val->value;
            }
        }

        return self::EN->value;
    }
}