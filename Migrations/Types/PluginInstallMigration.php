<?php

namespace WP_Migrations\Migrations\Types;

/**
 * Class PluginInstallMigration
 * Requires WP_CLI https://wp-cli.org
 * @package WP_Migrations\Migrations\Types
 */
abstract class PluginInstallMigration
{
    public function run()
    {
        $plugin = $this->get_plugin_name();

        $command = "cd " . ABSPATH . " && wp plugin install $plugin --activate";

        $output = exec($command);
    }

    /**
     * A plugin slug, the path to a local zip file, or URL to a remote zip file.
     * https://wp-cli.org/commands/plugin/install/
     * @return string <plugin slug|zip|url>
     */
    abstract function get_plugin_name();

}