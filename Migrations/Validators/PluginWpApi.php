<?php


namespace WP_Migrations\Migrations\Validators;


class PluginWpApi implements ValidatorInterface
{

    public static function validate($plugin)
    {
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
        ) );

        if ( is_wp_error( $api ) ) {
            return false;
        } else {
            return true;
        }
    }
}