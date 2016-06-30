<?php


namespace WP_Migrations;


class OptionVersionRepository
{
    /**
     * Create the migration repository data store.
     *
     * @return void
     */
    public static function createRepository()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . "optionversions";

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
          id BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          option_id BIGINT(20) NOT NULL,
          option_name VARCHAR(191) NOT NULL,
          option_value LONGTEXT NOT NULL,
          user_id mediumint(9),
          script varchar(255) NOT NULL,
          created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
          UNIQUE KEY id (id)
        ) $charset_collate;";
        $charset_collate = $wpdb->get_charset_collate();

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

}