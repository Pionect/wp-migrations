<?php

namespace WP_Migrations\Libraries;

class Helper
{
    public static function studly($value)
    {
        $value = ucwords(str_replace(['-', '_'], ' ', $value));

        return str_replace(' ', '', $value);
    }

    public static function get_plugin_data_by_name($pluginName)
    {
        $plugins = get_plugins();

        foreach ($plugins as $file => $data) {
            if (strpos($file, $pluginName . '/') >= 0) {
                return $data;
            }
        }

        return false;
    }

}