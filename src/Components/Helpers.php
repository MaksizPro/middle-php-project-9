<?php

namespace App\Components;

class Helpers
{
    public static function trimLongText(string $text, int $length = 200)
    {
        return mb_strimwidth($text, 0, $length, '...', 'UTF-8');
    }

    public static function trimLongTextInArray(array $arr, array $keys): array
    {
        foreach ($keys as $key) {
            foreach ($arr as $value) {
                if (isset($value[$key]) && is_string($value[$key])) {
                    $value[$key] = self::trimLongText($value[$key]);
                }
            }
        }
        return $arr;
    }
}
