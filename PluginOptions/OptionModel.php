<?php


namespace WP_Migrations\PluginOptions;


class OptionModel
{

    public $script;
    public $type;
    public $group;

    const TYPE_WORDPRESS = 'wordpress';
    const TYPE_PLUGIN = 'plugin';
    const TYPE_THEME = 'theme';
    const TYPE_UNKNOWN = 'unknown';

}