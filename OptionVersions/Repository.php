<?php


namespace WP_Migrations\OptionVersions;


class Repository
{
    const TABLE_NAME = "optionversions";


    /**
     * Create the migration repository data store.
     *
     * @return void
     */
    public static function createRepository()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . self::TABLE_NAME;

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
          id BIGINT(20) NOT NULL AUTO_INCREMENT,
          option_name VARCHAR(191) NOT NULL,
          option_value LONGTEXT NOT NULL,
          user_id mediumint(9),
          updated_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
          PRIMARY KEY (id)
        ) $charset_collate;";
        $charset_collate = $wpdb->get_charset_collate();

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    /**
     * Log an updated option
     *
     * @param  string $option
     * @param  string $option_value
     * @param  string $script
     * @param  int    $user_id
     * @return void
     */
    public function log($option,$option_value,$user_id=null)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . self::TABLE_NAME;

        $wpdb->insert(
            $table_name,
            [
                'option_name'  => $option,
                'option_value' => $option_value,
                'user_id'      => $user_id,
            ],
            ['%s', '%s', '%d']
        );
    }

    public function getOptionChanges(){
        global $wpdb;

        $table_name = $wpdb->prefix . self::TABLE_NAME;

        $query = "SELECT * FROM $table_name";

        return $wpdb->get_results($query);
    }
}