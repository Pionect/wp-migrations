<?php
/*
Plugin Name: WP Migrations
Description: Enable hardcoded migrations for every WordPress plugin option
Author: Pionect
Version: 0.0.1
Author URI: http://www.pionect.nl
Text Domain: wp_migrations
*/

/**
 * TODO
 *
 * - show real plugin names instead of folder names
 * - overwrite PluginOption in order of theme -> plugin -> wordpress core
 * - change repositories to singletons
 * - change the ListTable option_value to a short summary
 * - add ajax popup for the option_value
 * - add page with a list of all the migrations ran
 * - add a notice if the supplied migrations folder isn't correct
 **/

namespace WP_Migrations;

use Composer\Semver\Comparator;

include('Libraries/Autoloader.php');
include('vendor/autoload.php');

class Plugin
{
    const VERSION = '0.0.1';

    static function init()
    {
        register_activation_hook(__FILE__, array(static::class, 'plugin_activated'));

        add_action('admin_init', array(static::class, 'run_migrations'), 100);
        
        $optionVersionsProvider = new OptionVersions\Provider(
            new OptionVersions\Repository()
        );
        $optionVersionsProvider->init();

        $pluginOptionsProvider = new PluginOptions\Provider(
            new PluginOptions\Repository()
        );
        $pluginOptionsProvider->init();
    }

    static function plugin_activated()
    {
        $current_version = get_option('wp-migrations-version') ?: '0.0.0';

        if(Comparator::lessThan($current_version,'0.0.1')) {
            // initial installation
            Migrations\Repository::createRepository();
            OptionVersions\Repository::createRepository();
            PluginOptions\Repository::createRepository();
        }

        add_option('wp-migrations-version', self::VERSION);
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