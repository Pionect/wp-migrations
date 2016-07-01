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
        add_action('add_option', [$this, 'add_option']);
    }

    public function add_option($option)
    {
        if ($option[0] == '_') {
            return;
        }
        $files = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        $unit=array('b','kb','mb','gb','tb','pb');
        $size = memory_get_usage();
        echo round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];

        $script = '';
        if (count($files) >= 6 && array_key_exists('file',$files[5])) {
            $script = $files[5]['file'];
        }
        foreach($files as $file){
            if($file['function'] == 'add_site_option' && array_key_exists('file',$file)){
                $script = $file['file'];
            }
        }

        if(strpos($script,'wp-admin/options.php')){
            
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

        $object = (object)[
            'script' => $script,
            'type' => $type,
            'group' => $group
        ];

        $this->repository->save($option,$object);
    }
}