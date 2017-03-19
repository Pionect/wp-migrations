<?php

namespace WP_Migrations\Migrations\Validators;

use Composer\Semver\Semver;
use WP_Migrations\Libraries\Helper;

class PluginVersion implements ValidatorInterface
{
    public static function validate($value)
    {
        $valuePieces = explode('|', $value);

        if (count($valuePieces) != 2) {
            throw new Exception('PluginVersion validator expects a pipe seperated string with the plugin name and version');
        }

        $pluginName    = $valuePieces[0];
        $pluginVersion = $valuePieces[1];

        $data = Helper::get_plugin_data_by_name($pluginName);

        if (is_array($data)) {
            return Semver::satisfies($data['Version'], $pluginVersion);
        } else {
            // if the plugin isn't installed there is no version te be checked
            // this validator only checks the version, it doesn't check the existence of the plugin
            // so therefore it return true if the plugin isn't present
            return true;
        }

    }

}