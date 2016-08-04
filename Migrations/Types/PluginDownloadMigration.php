<?php

namespace WP_Migrations\Migrations\Types;


abstract class PluginDownloadMigration
{
    public function run()
    {
        $plugin = $this->get_plugin_name();

        include_once( ABSPATH . 'wp-admin/includes/file.php' );
        include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' ); //for plugins_api..

        $api = plugins_api( 'plugin_information', array(
            'slug' => $plugin,
            'fields' => array(
                'short_description' => false,
                'sections' => false,
                'requires' => false,
                'rating' => false,
                'ratings' => false,
                'downloaded' => false,
                'last_updated' => false,
                'added' => false,
                'tags' => false,
                'compatibility' => false,
                'homepage' => false,
                'donate_link' => false,
            ),
        ));

        $upgrader = new \Plugin_Upgrader( new Quiet_Skin() );
        $upgrader->install($api->download_link);
    }


    public function get_validation_rules(){
        return [
            'plugin_wp_api' => $this->get_plugin_name()
        ];
    }

    /**
     * Return the plugin slug
     * @return string plugin slug
     */
    abstract function get_plugin_name();

}

include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
class Quiet_Skin extends \WP_Upgrader_Skin {
    public function feedback($string)
    {
        // just keep it quiet
    }
}