<?php


namespace WP_Migrations\OptionVersions;


class Provider
{
    protected $repository;

    public function __construct(\WP_Migrations\OptionVersions\Repository $optionVersionRepository)
    {
        $this->repository = $optionVersionRepository;
    }

    public function init()
    {
        add_action('updated_option', [$this, 'updated_option'], 1, 3);
    }

    public function updated_option($option,$old_value, $new_value)
    {
        if ($option[0] == '_') {
            return;
        }

        $origin = $this->determine_origin();

        $serialized_option = maybe_serialize( $new_value );

        $this->repository->log($option,$serialized_option,$origin->type,$origin->script,get_current_user_id());
    }

    private function determine_origin(){
        $files = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        if (count($files) >= 4) {
            $script = $files[3]['file'];
        }

        foreach ($files as $i => $file) {
            // update_option exactly matches for regular locations
            if ($file['function'] == 'update_option') {
                $script = $file['file'];
            }
            // if the update option was fired in cron.php, we tag the first script responsible for triggering the cron
            if(strpos($script,'wp-includes') && array_key_exists('file',$file)){
                $script = $file['file'];
            }
        }

        if(strpos($script,'wp-admin/options.php')){
            $type = 'option~'.$_REQUEST['option_page'];
        } elseif (strpos($script, 'wp-admin')) {
            $type = 'wp-admin';
        } elseif (strpos($script, 'wp-content/plugins')) {
            $folders = explode(DIRECTORY_SEPARATOR,substr($script,strpos($script,'plugins/')));
            $type = 'plugin~'.$folders[1];
        } elseif (strpos($script, 'wp-content/themes')) {
            $folders = explode(DIRECTORY_SEPARATOR,substr($script,strpos($script,'themes/')));
            $type = 'theme~'.$folders[1];
        } else {
            $type = 'unknown';
        }

        return (object)[
            'script' => $script,
            'type' => $type
        ];
    }

    public static function toolsPage(){
        
    }

}