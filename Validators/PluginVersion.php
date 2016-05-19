<?php

namespace WP_Migrations\Validators;

use Composer\Semver\Semver;
use WP_Migrations\Libraries\Helper;

class PluginVersion implements ValidatorInterface
{
    public static function validate($value)
    {
        $version = Helper::plugin_version($value);

        return Semver::satisfies($version,$value);
    }

}