<?php

namespace Migrations;

class DisableSearchEngines extends \WP_Migrations\Migrations\Types\OptionMigration
{

    /**
     * Run the migrations.
     *
     * @param $option_value as returned from get_option
     * @return void
     */
    public function up($option_value)
    {
        return $option_value;
    }

    public function get_option_name()
    {
        return 'active_plugins';
    }

    public function get_validation_rules()
    {
        return [
            'plugin_exists'  => 'debug-bar',
            'plugin_version' => 'debug-bar|0.8.*'
        ];
    }
}