<?php

namespace WP_Migrations\Migrations\Validators;

use Composer\Semver\Semver;
use WP_Migrations\Libraries\Helper;

class PluginVersion implements ValidatorInterface
{
    public static function validate($value)
    {
        $data = Helper::get_plugin_data_by_name($value);

        if (is_array($data)) {
            return Semver::satisfies($data['Version'],$value);
        } else {
            // if the plugin isn't installed there is no version te be checked
            // this validator only checks the version, it doesn't check the existence of the plugin
            // so therefore it return true if the plugin isn't present
            return true;
        }

    }

}