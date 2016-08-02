<?php

namespace WP_Migrations\Migrations\Types;


abstract class PluginInstallMigration
{
    public function run()
    {
        $file = $this->get_filename();

        $command = "cd " . ABSPATH . " && wp plugin install $file --activate";

        $output = exec($command);
    }

    public function get_validation_rules()
    {
        return [
            'file_exists'   => $this->get_filename()
        ];
    }

    /**
     * @return string The option_name from the wp_options table
     */
    abstract function get_filename();

}