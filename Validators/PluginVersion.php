<?php

namespace WP_Migrations\Validators;

use Composer\Semver\Semver;
use WP_Migrations\Libraries\Helper;

class PluginVersion
{
    public static function validate($value)
    {
        $version = Helper::plugin_version($value);

        return Semver::satisfies($value, $version);
    }

}