<?php

namespace WP_Migrations\OptionVersions\Repositories;


class OptionScriptRepository
{
    const OPTION_NAME = "wp-migrations-option-scripts";

    private $options;

    public function __construct()
    {
        $optionValues  = get_option(self::OPTION_NAME);
        $this->options = $optionValues ?: [];
    }

    /**
     * Create the plugin options repository data store.
     *
     * @return void
     */
    public static function createRepository()
    {
        add_option(self::OPTION_NAME, []);
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Register a option's owner
     *
     * @param  string $optionName
     * @param  object $object
     * @return void
     */
    public function cache($optionName, $object)
    {
        $options = $this->getOptions();
        if (!array_key_exists($optionName, $options)) {
            $this->options[$optionName] = $object;
        }
    }

    public function save()
    {
        update_option(self::OPTION_NAME, $this->options);
    }

    public function get($optionName)
    {
        $options = $this->getOptions();
        if (!array_key_exists($optionName, $options)) {
            return null;
        } else {
            return $options[$optionName];
        }
    }


}