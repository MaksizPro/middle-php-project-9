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
        foreach ($keys as $value) {
            if(isset($arr[$value]) && is_string($arr[$value])){
                $arr[$value] = self::trimLongText($arr[$value]);
            }
        }
        return $arr;
    }
}
