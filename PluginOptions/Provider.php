<?php

namespace WP_Migrations\PluginOptions;


class Provider
{

    protected $repository;

    public function __construct(Repository $pluginOptionsRepository)
    {
        $this->repository = $pluginOptionsRepository;
    }

    public function init()
    {
        add_filter('all', [$this, 'pre_option_'],1,3);
        add_action('shutdown',[$this,'save_plugin_options'],1,0);
    }

    public function pre_option_($actionHook,$bool=false,$option=null)
    {
        if(strpos($actionHook,'pre_option_') === FALSE){
            return $bool;
        }

        // dont register values starting with _
        if ($option[0] == '_') {
            return;
        }

        $storedOptionValues = $this->repository->get($option);
        /* @var \WP_Migrations\PluginOptions\OptionModel $storedOptionValues */

        if(!is_null($storedOptionValues) && $storedOptionValues->type == 'wordpress'){
            return $bool;
        }

        $optionModel = $this->determineOptionValues();

        if(!is_null($storedOptionValues)){
            if($optionModel->type == OptionModel::TYPE_THEME || $optionModel->type == OptionModel::TYPE_UNKNOWN){
                //the option allready exists and this one can't supersede the stored one.
                return $bool;
            }
            if($optionModel->type == OptionModel::TYPE_PLUGIN &&
                in_array($storedOptionValues->type,[OptionModel::TYPE_PLUGIN,OptionModel::TYPE_WORDPRESS]) ){
                return $bool;
            }
            if($optionModel->type == OptionModel::TYPE_WORDPRESS &&
                $storedOptionValues->type == OptionModel::TYPE_WORDPRESS) {
                // types are equal so no need to supersede the stored one.
                return $bool;
            }
        }

        $this->repository->cache($option,$optionModel);

        return $bool;
    }

    public function save_plugin_options(){
        remove_filter( 'all', [$this, 'pre_option_'],1 );
        $this->repository->save();
    }

    private function determineOptionValues(){
        $files = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        $optionModel = new OptionModel();
        $optionModel->script = '';

        if (count($files) >= 5 && array_key_exists('file',$files[4])) {
            $optionModel->script = $files[4]['file'];
        }
        foreach($files as $file){
            if($file['function'] == 'get_option' && array_key_exists('file',$file)){
                $optionModel->script = $file['file'];
            }
        }

        if(strpos($optionModel->script,'wp-includes') || strpos($optionModel->script, 'wp-admin')){
            $optionModel->type = OptionModel::TYPE_WORDPRESS;
            $optionModel->group = 'wordpress';
        } elseif (strpos($optionModel->script, 'wp-content/plugins')) {
            $folders = explode(DIRECTORY_SEPARATOR,substr($optionModel->script,strpos($optionModel->script,'plugins/')));
            $optionModel->type = OptionModel::TYPE_PLUGIN;
            $optionModel->group = $folders[1];
        } elseif (strpos($optionModel->script, 'wp-content/themes')) {
            $folders = explode(DIRECTORY_SEPARATOR,substr($optionModel->script,strpos($optionModel->script,'themes/')));
            $optionModel->type = OptionModel::TYPE_THEME;
            $optionModel->group = $folders[1];
        } else {
            $optionModel->type = OptionModel::TYPE_UNKNOWN;
            $optionModel->group = OptionModel::TYPE_UNKNOWN;
        }

        $files = null;

        return $optionModel;
    }
}