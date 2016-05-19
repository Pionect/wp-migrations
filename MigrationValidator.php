<?php

namespace WP_Migrations;


use WP_Migrations\Libraries\Helper;

class MigrationValidator
{
    public function validate($migration)
    {
        $validation_rules = $migration->get_validation_rules();

        foreach ($validation_rules as $rule => $value) {
            $validator = $this->getValidator($rule);

            if($validator->validate($value) == false){
                return false;
            }
        }

        return true;
    }

    /**
     * @param $rule string
     * @return Validators\ValidatorInterface
     * @throws ValidatorMissingException
     * @throws ValidatorWithoutInterfaceException
     */
    private function getValidator($rule)
    {
        // see if the given validator is a full qualified name.
        if (class_exists($rule)) {
            $validator = new $rule;
        } else {
            //if the name isn't a FQN then try to see if it's a validator from us
            $validator_class_name = Helper::studly($rule);
            $namespace            = '\WP_Migrations\Validators\\';
            if (class_exists($namespace . $validator_class_name)) {
                $fqn       = $namespace . $validator_class_name;
                $validator = new $fqn;
            }
        }

        if (!$validator) {
            throw new ValidatorMissingException($rule);
        }
        if ($validator instanceof \WP_Migrations\Validators\ValidatorInterface == false) {
            throw new ValidatorWithoutInterfaceException($rule);
        }

        return $validator;
    }
}

class ValidatorMissingException extends \Exception {}
class ValidatorWithoutInterfaceException extends \Exception {}
