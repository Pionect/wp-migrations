<?php

namespace WP_Migrations\Migrations\Validators;


interface ValidatorInterface
{
    public static function validate($value);
}