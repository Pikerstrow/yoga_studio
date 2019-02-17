<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 17.01.2019
 * Time: 20:39
 */

namespace Core\Http;

use Core\Support\Checkout;

class Request
{
    use Checkout;

    const VALIDATORS_NAMESPACE = "Core\\Validators\\";

    protected $path;
    protected $domain;

    public function __construct()
    {
        $this->storage = array_merge($_POST, $_GET, $_FILES);
        $this->path = $_SERVER['REQUEST_URI'];
        $this->domain = $_SERVER['HTTP_HOST'];
    }

    public function getPath() :string
    {
        return $this->path;
    }

    public function getUrl() :string
    {
        return $this->domain . $this->path;
    }

    public function getDomain() :string
    {
        return $this->domain;
    }

    public function validate(array $rules) :array
    {
        $data = array();
        $temporary = array(); //тут будемо зберігати проміжні результати валідації у форматі: field_name = ['data' => 'value, 'error' => 'text of error']
        $validated = array(); //тут буде інформація для повернення в контроллер

        /*Перебираємо всі наявні значення в POST, GET, FILES, і у випадку, якщо ключ масива із правил співпадає із ключом в POST, GET, FILES - ми маємо поле
            до якого потрібно застосувати валідацію.
        */
        foreach ($this->storage as $key => $value){

            if(array_key_exists($key, $rules)){
                /*Перевіряємо чи існує відповідний клас в папці Validators. Якщо існує - створюємо обєкт класу і викликаємо метод валідації,
                який поверне передане значення та помилку у випадку її наявності.
                */
                if(class_exists($classname = static::VALIDATORS_NAMESPACE . ucfirst($key))){
                    $data['value'] = $this->storage[$key];
                    $data['rules'] = $rules[$key];

                    $object = new $classname($data);

                    $temporary[$key] = $object->validate();
                }
            }
        }

        /*Формуємо масив даних для контролера у форматі ['data' => ['field_1' => 'value', 'field_2' => 'value'],
            'errors' => ['field_1' => 'error', 'field_2' => 'error']]. Такий формат необхідний для зручного використання методів моделей.
        */
        foreach($temporary as $key => $value){
            $validated['data'][$key] = isset($value['data']) ?  $value['data'] : '';

            if(isset($value['error'])){
                $validated['errors'][$key] = $value['error'];
            }

        }

        return $validated;
    }

    public function hasFile($key)
    {
        if($this->has($key)){
            $fileArray = $this->get($key);

            return !empty($fileArray['name']);
        }
        return false;
    }

}