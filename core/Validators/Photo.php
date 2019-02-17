<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 02.02.2019
 * Time: 10:45
 */

namespace Core\Validators;


class Photo extends Field
{
    use FileDownloadErrors;

    protected $name = 'Фото';
    protected $extensions = ['png', 'jpeg', 'jpg'];

    public function validate()
    {
        /*Використання трейту FileDownloadErrors*/
        if($this->validated['error'] = $this->checkDownload($this->value)){
            return $this->validated;
        }

        foreach ($this->rules as $rule) {
            switch($rule){
                case "required":
                    if($this->value['name'] == "" or empty($this->value['name'])) {
                        $this->validated['error'] = str_replace(":name", $this->name, $this->messages['required']);
                        return $this->validated;
                    }
                    break;
                case (strpos($rule, "maxsize") !== false):
                    if(floor(($this->value['size'])/1024) > explode(":", $rule)[1]) {
                        $this->validated['error'] = str_replace([":name", ":val"], [$this->name, explode(":", $rule)[1]], $this->messages['maxsize']);
                        return $this->validated;
                    }
                    break;
                case (strpos($rule, "maxheight") !== false):
                    if(getimagesize($this->value['tmp_name']) !== false and getimagesize($this->value['tmp_name'])[1] > explode(":", $rule)[1]) {
                        $this->validated['error'] = str_replace([":name", ":val"], [$this->name, explode(":", $rule)[1]], $this->messages['maxheight']);
                        return $this->validated;
                    }
                    break;
                case (strpos($rule, "maxwidth") !== false):
                    if(getimagesize($this->value['tmp_name']) !== false and getimagesize($this->value['tmp_name'])[0] > explode(":", $rule)[1]) {
                        $this->validated['error'] = str_replace([":name", ":val"], [$this->name, explode(":", $rule)[1]], $this->messages['maxwidth']);
                        return $this->validated;
                    }
                    break;
                case (strpos($rule, "minheight") !== false):
                    if(getimagesize($this->value['tmp_name']) !== false and getimagesize($this->value['tmp_name']) < explode(":", $rule)[1]) {
                        $this->validated['error'] = str_replace([":name", ":val"], [$this->name, explode(":", $rule)[1]], $this->messages['minheight']);
                        return $this->validated;
                    }
                    break;
                case (strpos($rule, "minwidth") !== false):
                    if(getimagesize($this->value['tmp_name']) !== false and getimagesize($this->value['tmp_name'])[0] < explode(":", $rule)[1]) {
                        $this->validated['error'] = str_replace([":name", ":val"], [$this->name, explode(":", $rule)[1]], $this->messages['minwidth']);
                        return $this->validated;
                    }
                    break;
                case (strpos($rule, "image") !== false):
                    $ext = mb_strtolower(pathinfo($this->value['name'], PATHINFO_EXTENSION));
                    if (!in_array($ext, $this->extensions) or getimagesize($this->value['tmp_name']) === false) {
                        $this->validated['error'] = str_replace(":name", $this->name, $this->messages['image']);
                        return $this->validated;
                    }
                    break;
                case (strpos($rule, "proportions") !== false):
                    $proportions = explode(":", $rule)[1];
                    $width = explode("*", $proportions)[0];
                    $height = explode("*", $proportions)[1];

                    if(getimagesize($this->value['tmp_name']) !== false and getimagesize($this->value['tmp_name'])[0] != $width and getimagesize($this->value['tmp_name'])[1] != $height) {
                        $this->validated['error'] = str_replace([":name", ":width", ":height"], [$this->name, $width, $height], $this->messages['proportions']);
                        return $this->validated;
                    }
                    break;
            }

        }
        return $this->validated;
    }
}