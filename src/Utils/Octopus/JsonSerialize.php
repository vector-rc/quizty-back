<?php

namespace Quizty\Utils\Octopus;

class JsonSerialize
{

    public static function encode($array)
    {
        $ident_array = self::fix_array($array);

        return json_encode($ident_array, JSON_NUMERIC_CHECK);
    }

    private static function fix_array($array)
    {
        $fixed_array = $array;

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $fixed_array[$key] = self::fix_array($value);
            } else if (is_object($value)) {
                $fixed_array[$key] = self::fix_array((array)$value);
            } else if (is_string($value)) {
                $json = json_decode($value);
                if ($json) {
                    $fixed_array[$key] = $json;
                } else {
                    $fixed_array[$key] = $value;
                }
            }
        }

        return $fixed_array;
    }
}
