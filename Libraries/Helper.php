<?php

namespace WP_Migrations\Libraries;

class Helper
{
    public static function studly($value)
    {
        $value = ucwords(str_replace(['-', '_'], ' ', $value));

        return str_replace(' ', '', $value);
    }
}