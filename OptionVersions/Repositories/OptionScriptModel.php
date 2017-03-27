<?php


namespace WP_Migrations\OptionVersions\Repositories;


class OptionScriptModel implements \Serializable
{

    public $script;
    public $type;
    public $group;

    const TYPE_WORDPRESS = 'wordpress';
    const TYPE_PLUGIN    = 'plugin';
    const TYPE_THEME     = 'theme';
    const TYPE_UNKNOWN   = 'unknown';


    public function serialize()
    {

        return serialize([
            'script' => $this->script,
            'type'   => $this->type,
            'group'  => $this->group
        ]);
    }

    public function unserialize($data)
    {
        $data         = unserialize($data);
        $this->script = $data['script'];
        $this->type   = $data['type'];
        $this->group  = $data['group'];
    }

}