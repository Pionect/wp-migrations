<?php

namespace WP_Migrations\Migrations\Validators;


class FileExists implements ValidatorInterface
{
    public static function validate($filename)
    {
        return file_exists($filename);
    }
}