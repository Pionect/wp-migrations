<?php

namespace WP_Migrations\Libraries;

class Helper
{
    public static function studly($value)
    {
        $value = ucwords(str_replace(['-', '_'], ' ', $value));

        return str_replace(' ', '', $value);
    }

    public static function get_plugin_data_by_name($plugin_name)
    {
        $plugins = get_plugins();

        foreach ($plugins as $file => $data) {
            if (strpos($file, $plugin_name . '/') >= 0) {
                return $data;
            }
        }

        return false;
    }

    public static function is_plugin_installed($plugin_name)
    {
        return is_array(static::get_plugin_data_by_name($plugin_name));
    }

    public static function plugin_version($plugin_name)
    {
        $data = static::get_plugin_data_by_name($plugin_name);
        if (is_array($data)) {
            return $data['Version'];
        } else {
            return false;
        }

    }
}