<?php

namespace WP_Migrations\Migrations\Validators;

use WP_Migrations\Libraries\Helper;

class PluginExists implements ValidatorInterface
{
    public static function validate($value){

        $plugin_name = $value;
        return is_array(Helper::get_plugin_data_by_name($plugin_name));

    }
}