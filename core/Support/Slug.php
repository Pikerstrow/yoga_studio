<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 05.02.2019
 * Time: 19:53
 */

namespace Core\Support;


trait Slug
{
    protected $ukrainian = [
        "А","а","Б","б","В","в","Г","г","Ґ","ґ","Д","д","Е","е",
        "Є","є","Ж","ж","З","з","И","и","І","і","Ї","ї","Й","й",
        "К","к","Л","л","М","м","Н","н","О","о","П","п","Р","р",
        "С","с","Т","т","У","у","Ф","ф","Х","х","Ц","ц","Ч","ч",
        "Ш","ш","Щ","щ","Ь","ь","Ю","ю","Я","я"
    ];

    protected $english = [
        "a","a","b","b","v","v","h","h","g","g","d","d","e","e",
        "ye","ie","zh","zh","z","z","y","y","i","i","yi","i","y","i",
        "k","k","l","l","m","m","n","n","o","o","p","p","r","r",
        "s","s","t","t","u","u","f","f","kh","kh","ts","ts","ch","ch",
        "sh","sh","shch","shch","","","yu","iu","ya","ia"
    ];

    public function slug($string)
    {
        /*Прибираємо можливі комбінації із тире та пробілами перед та/або після тире*/
        $string = preg_replace("#\s\-\s|\-\s|\s\-#", " ", $string);
        /*Міняємо пробіли на дефіс*/
        $string = preg_replace("#\s#", "-", $string);
        /*Замінюємо інші символи, які не доцільно використовувати в URL*/
        $string = preg_replace("#,|\.|\!|\?|\"|\@|\#|№|\\(|\\)|\+|\=|\*|{|}|\$|\&|%|:|;|\\]|\\[|\/#", "", $string);

        return str_replace($this->ukrainian, $this->english, $string);
    }
}