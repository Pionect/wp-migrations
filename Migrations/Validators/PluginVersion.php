<?php

namespace WP_Migrations\Migrations\Validators;

use Composer\Semver\Semver;
use WP_Migrations\Libraries\Helper;

class PluginVersion implements ValidatorInterface
{
    public static function validate($value)
    {
        $version = Helper::plugin_version($value);

        // if the plugin isn't installed there is no version te be checked
        // this validator only checks the version, it doesn't check the existence of the plugin
        // so therefore it return true if the plugin isn't present
        if($version == false){
            return true;
        }

        return Semver::satisfies($version,$value);
    }

}