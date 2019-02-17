<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 05.02.2019
 * Time: 19:22
 */

namespace Core\Validators;

/**
 * Trait FileDownloadErrors
 * @package Core\Validators
 * Винесено в трейт, так як може бути успішно застосоване для любого типу файлів
 */
trait FileDownloadErrors
{
    public function checkDownload($value)
    {
        if ($value['error'] != 0) {
            switch ($value['error']) {
                case 1 :
                    return 'Розмір файлу перевищує допустимі значення';
                case 2 :
                    return 'Розмір файлу перевищує допустимі значення';
                case 3 :
                    return 'Помилка передачі файлу. Файл передано лише частково';
                case 4 :
                    return 'Файл не було завантажено! Файл є обов\'язковим';
                case 6 :
                    return 'На сервері відсутня директорія для завантаження зображення.';
                case 7 :
                    return 'Не вдалося записати файл на диск.';
                case 8 :
                    return 'Помилка завантаження файлу.';
            }
        }
        return null;
    }
}