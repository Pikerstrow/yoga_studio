<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 02.02.2019
 * Time: 11:45
 */

namespace Core\Validators;


class Password extends Field
{
    protected $name = "Пароль";
    protected $saltSymbols = '!@#$%^&*`()_=+-:;"/\\~';

    public function validate()
    {
        parent::validate();

        foreach ($this->rules as $rule) {
            switch($rule){
                case "special":
                    if(!strpbrk($this->value, $this->saltSymbols)){
                        $this->validated['error'] = str_replace(":name", $this->name, $this->messages['special']);
                        return $this->validated;
                    }
                    break;
                case "special+number":
                    if(!strpbrk($this->value, $this->saltSymbols) or !preg_match('#[0-9]#', $this->value)){
                        $this->validated['error'] = str_replace(":name", $this->name, $this->messages['special+number']);
                        return $this->validated;
                    }
                    break;
                case "latin":
                    if(preg_match('#[а-яА-Я]|ёіІ#ui', $this->value)){
                        $this->validated['error'] = str_replace(":name", $this->name, $this->messages['latin']);
                        return $this->validated;
                    }
                    break;
            }
        }

        return $this->validated;
    }

}