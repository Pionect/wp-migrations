<?php

namespace WP_Migrations\OptionVersions\Repositories;


class OptionScriptRepository
{
    const OPTION_NAME = "wp-migrations-option-scripts";

    private $options;

    public function __construct()
    {
        $optionValues = get_option(self::OPTION_NAME);
        if (is_array($optionValues)) {
            foreach ($optionValues as $option_name => $optionScriptData) {
                $optionScript = new OptionScriptModel();
                $optionScript->unserialize($optionScriptData);
                $optionValues[$option_name] = $optionScript;
            }
        }
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
        $options = [];
        foreach ($this->options as $option_name => $optionScript) {
            $options[$option_name] = $optionScript->serialize();
        }
        update_option(self::OPTION_NAME, $options);
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