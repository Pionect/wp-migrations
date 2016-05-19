<?php

namespace WP_Migrations\Validators;


interface ValidatorInterface
{
    public static function validate($value);
}