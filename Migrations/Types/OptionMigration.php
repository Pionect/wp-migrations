<?php

namespace WP_Migrations\Migrations\Types;


abstract class OptionMigration
{
    public function run()
    {
        $option_value = get_option($this->get_option_name());

        $new_option_value = $this->up($option_value);

        update_option($this->get_option_name(), $new_option_value);
    }

    /**
     * @return string The option_name from the wp_options table
     */
    abstract function get_option_name();

    /**
     * @param $option_value
     * @return mixed
     */
    abstract function up($option_value);

}