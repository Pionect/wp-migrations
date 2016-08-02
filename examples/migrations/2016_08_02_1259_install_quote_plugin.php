<?php

namespace App\Structure\Migrations;

use WP_Migrations\Migrations\Types\PluginInstallMigration;

class InstallQuotePlugin extends PluginInstallMigration
{
    function get_filename(){
        //somewhere outside the webroot
        return ABSPATH. '/../woocommerce-request-a-quote.zip';
    }

    public function get_validation_rules()
    {
        $default_rules = parent::get_validation_rules();
        $custom_rules = [
            'plugin_exists'  => 'woocommerce'
        ];
        return array_merge($custom_rules,$default_rules);
    }

}