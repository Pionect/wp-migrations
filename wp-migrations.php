<?php
/*
Plugin Name: WP Migrations
Description: Enable hardcoded migrations for every WordPress plugin option
Author: Pionect
Version: 0.0.1
Author URI: http://www.pionect.nl
Text Domain: wp_migrations
*/

namespace WPMigrations;

include('Libraries/Autoloader.php');

class Plugin
{
    const VERSION = '0.0.1';

    static function init()
    {
        register_activation_hook( __FILE__, array( Install::class, 'run' ) );

        add_action( 'admin_init', array( static::class, 'run_migrations' ), 100);
    }

    static function run_migrations()
    {
        $directory = apply_filters( 'wpmigrations_directory', null);

        if(is_null($directory)){
            return;
        }

        $migrationHandler = new MigrationHandler();
        $migrationHandler->findMigrations($directory);
    }

}

Plugin::init();