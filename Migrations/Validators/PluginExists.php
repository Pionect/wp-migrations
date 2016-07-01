<?php

namespace WP_Migrations\Migrations\Validators;

use WP_Migrations\Libraries\Helper;

class PluginExists implements ValidatorInterface
{
    public static function validate($value){
        
        return Helper::is_plugin_installed($value);
    }
}