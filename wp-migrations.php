<?php
/*
Plugin Name: WP Migrations
Description: Enable hardcoded migrations for every WordPress plugin option
Author: Pionect
Version: 0.0.1
Author URI: http://www.pionect.nl
Text Domain: wp_migrations
*/

namespace WP_Migrations;

include('Libraries/Autoloader.php');
include('vendor/autoload.php');

class Plugin
{
    const VERSION = '0.0.1';

    static function init()
    {
        register_activation_hook(__FILE__, array(static::class, 'plugin_activated'));

        add_action('admin_init', array(static::class, 'run_migrations'), 100);
        add_action('admin_menu', array(static::class, 'optionversions_menu'));
        static::initialize_option_versions_provider();
    }

    static function plugin_activated()
    {
        Migrations\Repository::createRepository();
        OptionVersions\Repository::createRepository();
    }

    static function initialize_option_versions_provider()
    {
        $optionVersionsProvider = new OptionVersions\Provider(
            new OptionVersions\Repository()
        );
        $optionVersionsProvider->init();
    }

    static function optionversions_menu(){
        $title = __("Option Versions",'wp-migrations');
        add_management_page( $title, $title, 'manage_options', 'optionversions', [OptionVersions\Provider::class,'toolsPage'] );
    }

    static function run_migrations()
    {
        $directory = apply_filters('wpmigrations_directory', null);
        $namespace = apply_filters('wpmigrations_namespace', 'Migrations');

        if (is_null($directory)) {
            return;
        }

        $migrationHandler = new Migrations\Migrator(
            new Migrations\Repository(),
            new Migrations\Validator(),
            $namespace
        );
        $migrationHandler->run($directory);
    }

}

Plugin::init();