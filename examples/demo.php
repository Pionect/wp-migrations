<?php
/*
Plugin Name: WP Migrations demo
Author: Pionect
Version: 0.0.1
Author URI: http://www.pionect.nl
Text Domain: wp_migrations_demo
*/

/* Place these lines in your plugin or in theme functions.php */

add_filter( 'wpmigrations_directory', 'my_wpmigrations_directory' );

function my_wpmigrations_directory($directory){
    return __DIR__.'/migrations';
}