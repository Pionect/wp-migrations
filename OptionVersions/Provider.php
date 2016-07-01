<?php


namespace WP_Migrations\OptionVersions;


class Provider
{
    protected $repository;

    public function __construct(Repository $optionVersionRepository)
    {
        $this->repository = $optionVersionRepository;
    }

    public function init()
    {
        add_action('updated_option', [$this, 'updated_option'], 1, 3);
        add_action('admin_menu', [$this, 'optionversions_menu']);
    }

    public function updated_option($option,$old_value, $new_value)
    {
        if ($option[0] == '_') {
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

        include('assets/toolspage.php');
    }

}