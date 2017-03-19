<?php

namespace App\Structure\Migrations;

use WP_Migrations\Migrations\Types\PluginDownloadMigration;

class InstallWoocommercePlugin extends PluginDownloadMigration
{
    function get_plugin_name(){
        return 'woocommerce';
    }

}