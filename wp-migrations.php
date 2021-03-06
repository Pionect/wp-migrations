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
 * - fix fatal error, when mu-plugin & WPI cli call is made
 * - migrations creator - pick two versions and create a migration
 * - add tabs to the value modal [raw, maybe_unserialized, diff]
 * - show real plugin names instead of folder names
 * - change repositories to singletons
 * - add page with a list of all the migrations ran
 * - add a notice if the supplied migrations folder isn't correct
 * - add a setting to enable/disable optionVersions
 **/

namespace WP_Migrations;

use Composer\Semver\Comparator;
use WP_Migrations\OptionVersions\Repositories\OptionScriptRepository;
use WP_Migrations\OptionVersions\Repositories\OptionVersionRepository;

include('vendor/autoload.php');

class Plugin
{
    const VERSION = '0.0.1';

    static function init()
    {
        if(!class_exists('\WP_Migrations\OptionVersions\Tracking\OptionScriptProvider')){
            return;
        }
    
        if(!get_option('siteurl')){
            return;
        }
    
        static::plugin_upgrade();
    
        add_action('admin_init', array(static::class, 'run_migrations'), 100);
    
    
        $optionScriptTrackingProvider = new OptionVersions\Tracking\OptionScriptProvider(
            new OptionScriptRepository()
        );
        $optionScriptTrackingProvider->init();

        $optionVersionTrackingProvider = new OptionVersions\Tracking\OptionVersionProvider(
            new OptionVersionRepository()
        );
        $optionVersionTrackingProvider->init();

        $optionVersionsUIProvider = new OptionVersions\UserInterface\Provider(
            new OptionVersionRepository()
        );
        $optionVersionsUIProvider->init();
    }

    static function plugin_upgrade()
    {
        $currentVersion = get_option('wp-migrations-version') ?: '0.0.0';

        if(Comparator::lessThan($currentVersion,'0.0.1')) {
            // initial installation
            Migrations\Repository::createRepository();
            OptionVersionRepository::createRepository();
            OptionScriptRepository::createRepository();
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