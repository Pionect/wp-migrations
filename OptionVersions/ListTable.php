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

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = array();
        $this->_column_headers = array($columns, $hidden, $sortable);

        $pluginOptions = $pluginOptionsRepository->getPluginOptions();

        foreach($optionVersionsRepository->getOptionChanges() as $optionChange){
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
}