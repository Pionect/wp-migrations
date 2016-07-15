<?php


namespace WP_Migrations\OptionVersions;


use WP_List_Table;

class ListTable extends WP_List_Table
{

    function get_columns(){
        $columns = array(
            'type'       => __('type','wp-migrations'),
            'group'      => __('group','wp-migrations'),
            'option'     => __('option','wp-migrations'),
            'value'      => __('value','wp-migrations'),
            'user_id'    => __('User ID','wp-migrations'),
            'updated_at' => __('updated at','wp-migrations')
        );
        return $columns;
    }

    function prepare_items() {

        $pluginOptionsRepository = new \WP_Migrations\PluginOptions\Repository();
        $optionVersionsRepository = new \WP_Migrations\OptionVersions\Repository();

        $per_page = 20;
        $current_page = $this->get_pagenum();
        $total_items = $optionVersionsRepository->getCount();

        $this->set_pagination_args( array(
            'total_items' => $total_items,
            'per_page'    => $per_page
        ) );

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);

        $pluginOptions = $pluginOptionsRepository->getPluginOptions();

        $filter = ( ! empty($_REQUEST['group-filter'] ) ) ? $_REQUEST['group-filter'] : false;
        $filter_options = [];
        if($filter) {
            foreach ($pluginOptions as $option_name => $pluginOption) {
                if ($pluginOption->group == $filter) {
                    $filter_options[] = $option_name;
                }
            }
        }

        $order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'desc';
        $current_limit = ($current_page-1)*$per_page;

        $optionChanges = $optionVersionsRepository->getOptionChanges($current_limit,$per_page,$order,$filter_options);

        foreach($optionChanges as $optionChange){
            if(array_key_exists($optionChange->option_name,$pluginOptions)){
                $pluginOption = $pluginOptions[$optionChange->option_name];
            } else {
                $pluginOption = (object)[
                    'type' => null,
                    'group' => null
                ];
            }

            $this->items[] = [
                'type'       => $pluginOption->type,
                'group'      => $pluginOption->group,
                'option'     => $optionChange->option_name,
                'value'      => $optionChange->option_value,
                'user_id'    => $optionChange->user_id,
                'updated_at' => $optionChange->updated_at
            ];
        }
    }

    function column_default( $item, $column_name ) {
        switch( $column_name ) {
            case 'type':
            case 'group':
            case 'option':
            case 'value':
            case 'user_id':
            case 'updated_at':
                return $item[ $column_name ];
            default:
                return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
        }
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'updated_at'   => array('updated_at',true)
        );
        return $sortable_columns;
    }

    function extra_tablenav( $which ) {
        $pluginOptionsRepository = new \WP_Migrations\PluginOptions\Repository();
        $optionVersionsRepository = new \WP_Migrations\OptionVersions\Repository();

        $types = [];
        foreach($pluginOptionsRepository->getPluginOptions() as $option_name => $pluginOption){
            $count = $optionVersionsRepository->getCount($option_name);
            if($count) {
                $types[$pluginOption->type][] = $pluginOption->group;
            }
        }
        foreach($types as $type => $groups){
            sort($groups);
            $types[$type] = array_unique($groups);
        }

        $order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'desc';

        if ( $which == "top" ){
            include 'assets/table_top.php';
        }
    }
}