<?php

namespace WP_Migrations\PluginOptions;


class Provider
{

    protected $repository;

    public function __construct(Repository $pluginOptionsRepository)
    {
        $this->repository = $pluginOptionsRepository;
        //pull option value before we register the filter.
        $this->repository->getPluginOptions();
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

        //check if the option isn't allready registered
        if($this->repository->isRegistered($option)){
            return $bool;
        }

        $files = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        $script = '';
        if (count($files) >= 5 && array_key_exists('file',$files[4])) {
            $script = $files[4]['file'];
        }
        foreach($files as $file){
            if($file['function'] == 'get_option' && array_key_exists('file',$file)){
                $script = $file['file'];
            }
        }

        if(strpos($script,'wp-includes')){
            $type = 'wordpress';
            $group = 'wp-includes';
        } elseif (strpos($script, 'wp-admin')) {
            $type = 'wordpress';
            $group = 'wp-admin';
        } elseif (strpos($script, 'wp-content/plugins')) {
            $folders = explode(DIRECTORY_SEPARATOR,substr($script,strpos($script,'plugins/')));
            $type = 'plugin';
            $group = $folders[1];
        } elseif (strpos($script, 'wp-content/themes')) {
            $folders = explode(DIRECTORY_SEPARATOR,substr($script,strpos($script,'themes/')));
            $type = 'theme';
            $group = $folders[1];
        } else {
            $type = 'unknown';
            $group = 'unknown';
        }

        $files = null;

        $object = (object)[
            'script' => $script,
            'type' => $type,
            'group' => $group
        ];

        $this->repository->cache($option,$object);

        return $bool;
    }

    public function save_plugin_options(){
        remove_filter( 'all', [$this, 'pre_option_'],1 );
        $this->repository->save();
    }
}