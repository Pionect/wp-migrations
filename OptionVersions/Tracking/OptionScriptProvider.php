<?php

namespace WP_Migrations\OptionVersions\Tracking;


use WP_Migrations\OptionVersions\Repositories\OptionScriptModel;
use WP_Migrations\OptionVersions\Repositories\OptionScriptRepository;

class OptionScriptProvider
{

    /**
     * When an option is fetched we'll try to get a clue which script is fetching it.
     * it is hard to find out which scripts was responsible when an option is saved
     * so we'll try to connect the option with a script when the script (plugin/theme) requests for it.
     *
     * There's only one issue: there is no general filter in get_option, there is a filter pre_option_$optionname
     * So we hook into all filters and only continue our quest if the filters name matches pre_option
     *
     */

    protected $repository;

    public function __construct(OptionScriptRepository $OptionScriptRepository)
    {
        $this->repository = $OptionScriptRepository;
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

        $storedOptionScript = $this->repository->get($option);
        /* @var OptionScriptModel $storedOptionScript */


        if(!is_null($storedOptionScript) && $storedOptionScript->type == 'wordpress'){
            return $bool;
        }

        $optionModel = $this->determineOptionValues();

        if(!is_null($storedOptionScript)){
            if($optionModel->type == OptionScriptModel::TYPE_THEME || $optionModel->type == OptionScriptModel::TYPE_UNKNOWN){
                //the option allready exists and this one can't supersede the stored one.
                return $bool;
            }
            if($optionModel->type == OptionScriptModel::TYPE_PLUGIN &&
                in_array($storedOptionScript->type,[OptionScriptModel::TYPE_PLUGIN, OptionScriptModel::TYPE_WORDPRESS]) ){
                return $bool;
            }
            if($optionModel->type == OptionScriptModel::TYPE_WORDPRESS &&
                $storedOptionScript->type == OptionScriptModel::TYPE_WORDPRESS) {
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

        $optionModel = new OptionScriptModel();
        $optionModel->script = '';

        foreach($files as $file){
            if($file['function'] == 'get_option' && array_key_exists('file',$file)){
                $optionModel->script = $file['file'];
            }
        }
        if($optionModel->script == ""){
            
        }

        if(strpos($optionModel->script,'wp-includes') || strpos($optionModel->script, 'wp-admin')){
            $optionModel->type = OptionScriptModel::TYPE_WORDPRESS;
            $optionModel->group = 'wordpress';
        } elseif (strpos($optionModel->script, 'wp-content'.DIRECTORY_SEPARATOR.'plugins')) {
            $folders = explode(DIRECTORY_SEPARATOR,substr($optionModel->script,strpos($optionModel->script,'plugins'.DIRECTORY_SEPARATOR)));
            $optionModel->type = OptionScriptModel::TYPE_PLUGIN;
            $optionModel->group = $folders[1];
        } elseif (strpos($optionModel->script, 'wp-content'.DIRECTORY_SEPARATOR.'themes')) {
            $folders = explode(DIRECTORY_SEPARATOR,substr($optionModel->script,strpos($optionModel->script,'themes'.DIRECTORY_SEPARATOR)));
            $optionModel->type = OptionScriptModel::TYPE_THEME;
            $optionModel->group = $folders[1];
        } else {

            $optionModel->type = OptionScriptModel::TYPE_UNKNOWN;
            $optionModel->group = OptionScriptModel::TYPE_UNKNOWN;
        }

        $files = null;

        return $optionModel;
    }
}