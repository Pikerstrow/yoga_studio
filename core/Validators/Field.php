<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 02.02.2019
 * Time: 11:04
 */

namespace Core\Validators;


abstract class Field
{
    protected $value = null;
    protected $messages = [];
    protected $rules = [];
    protected $validated = []; // тут будуть дані після валідаці: передане користувачем значення + помилки, у випадку невдалої валідації

    public function __construct(array $data)
    {
        $this->value = $data['value'];
        $this->rules = explode('|', $data['rules']);
        $this->messages = include("messages.php");
        $this->validated['data'] = $this->value;
    }

    public function validate()
    {
        foreach ($this->rules as $rule) {
            switch($rule){
                case "required":
                    if($this->value == "" or empty($this->value)) {
                        $this->validated['error'] = str_replace(":name", $this->name, $this->messages['required']);
                        return $this->validated;
                    }
                    break;
                case (strpos($rule, "min") !== false):
                    if(mb_strlen($this->value) < explode(":", $rule)[1]) {
                        $this->validated['error'] = str_replace([":name", ":val"], [$this->name, explode(":", $rule)[1]], $this->messages['min']);
                        return $this->validated;
                    }
                    break;
                case (strpos($rule, "max") !== false):
                    if(mb_strlen($this->value) > explode(":", $rule)[1]) {
                        $this->validated['error'] = str_replace([":name", ":val"], [$this->name, explode(":", $rule)[1]], $this->messages['max']);
                        return $this->validated;
                    }
                    break;
            }
        }

        return $this->validated;
    }
}