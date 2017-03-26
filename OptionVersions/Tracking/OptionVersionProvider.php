<?php


namespace WP_Migrations\OptionVersions\Tracking;


use WP_Migrations\OptionVersions\Repositories\OptionVersionRepository;

class OptionVersionProvider
{
    protected $repository;

    public function __construct(OptionVersionRepository $optionVersionRepository)
    {
        $this->repository = $optionVersionRepository;
    }

    public function init()
    {
        add_action('updated_option', [$this, 'updated_option'], 1, 3);
    }

    public function updated_option($option, $old_value, $new_value)
    {
        if ($option[0] == '_') {
            return;
        }
        if ('wp-migrations-option-scripts' === $option) {
            return;
        }

        $serialized_option = maybe_serialize($new_value);

        $this->repository->log($option, $serialized_option, get_current_user_id());
    }

}