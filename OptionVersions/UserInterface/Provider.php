<?php


namespace WP_Migrations\OptionVersions\UserInterface;


use WP_Migrations\OptionVersions\Repositories\OptionVersionRepository;

class Provider
{
    protected $repository;

    public function __construct(OptionVersionRepository $optionVersionRepository)
    {
        $this->repository = $optionVersionRepository;
    }

    public function init()
    {
        add_action('updated_option', [$this, 'updated_option'], 1, 3);
        add_action('admin_menu', [$this, 'optionversions_menu']);
        add_action('wp_ajax_wp-migrations-value-modal', array(&$this, 'ajaxValueModalCallback'));

    }

    public function updated_option($option,$old_value, $new_value)
    {
        if ($option[0] == '_') {
            return;
        }
        if ('wp-migrations-plugin-options' === $option) {
            return;
        }

        $serialized_option = maybe_serialize( $new_value );

        $this->repository->log($option,$serialized_option,get_current_user_id());
    }

    public function optionversions_menu(){
        $title = __("Option Versions",'wp-migrations');
        add_management_page( $title, $title, 'manage_options', 'optionversions', [$this,'toolsPage'] );
    }

    public function toolsPage(){
        if( ! class_exists( 'WP_List_Table' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
        }

        $listTable = new ListTable();
        $listTable->prepare_items();

        add_thickbox();

        include('assets/toolspage.php');
    }

    function ajaxValueModalCallback()
    {
        global $wpdb;

        $optionVersionId = filter_input(INPUT_GET,'optionversion_id');

        $optionVersion = $this->repository->getById($optionVersionId);

        if(!$optionVersion){
            return '404 - This value couldn\'t be found';
        }

        $content = maybe_unserialize($optionVersion->option_value);

        echo ($content =="" ? '*empty*' : '<pre>'.print_r($content,true).'</pre>');
        die(); // this is required to return a proper result

    }

}