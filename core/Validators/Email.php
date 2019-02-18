<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 02.02.2019
 * Time: 10:45
 */

namespace Core\Validators;


class Email extends Field
{
    protected $name = 'Email';

    public function validate()
    {
        parent::validate();

        foreach ($this->rules as $rule) {
            switch($rule){
                case "email":
                    if(!filter_var($this->value, FILTER_VALIDATE_EMAIL)){
                        $this->validated['error'] = str_replace(":name", $this->name, $this->messages['email']);
                        return $this->validated;
                    }
                    break;
            }
        }

        return $this->validated;
    }

}