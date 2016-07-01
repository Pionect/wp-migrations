<?php

namespace WP_Migrations\PluginOptions;


class Repository
{
    const OPTION_NAME = "wp-migrations-plugin-options";

    private $pluginOptions;

    /**
     * Create the plugin options repository data store.
     *
     * @return void
     */
    public static function createRepository()
    {
        \add_option(self::OPTION_NAME, []);
    }

    /**
     * @return array
     */
    public function getPluginOptions()
    {
        if (is_null($this->pluginOptions)) {
            $option_values       = get_option(self::OPTION_NAME);
            $this->pluginOptions = $option_values ?: [];
        }

        return $this->pluginOptions;
    }

    /**
     * Register a option's owner
     *
     * @param  string $option_name
     * @param  string $owner
     * @return void
     */
    public function save($option_name,$object)
    {
        $pluginOptions = $this->getPluginOptions();
        if(!array_key_exists($option_name,$pluginOptions)) {
            $pluginOptions[$option_name] = $object;
            update_option(self::OPTION_NAME, $pluginOptions);
        }
    }

}