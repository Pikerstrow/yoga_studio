<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 05.02.2019
 * Time: 19:37
 */

namespace Core\Support;


class Cleaner
{
    public static function stripTags($data)
    {
        $cleanData = [];

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $cleanData[$key] = strip_tags($value);
            }
            return $cleanData;
        }

        return strip_tags((string)$data);
    }
}