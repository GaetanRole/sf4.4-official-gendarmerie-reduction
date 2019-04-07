<?php

/**
 * Slugger util file
 *
 * @category    Slugger
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */

namespace App\Utils;

final class Slugger
{
    /**
     * Slugify a title
     *
     * @param string $string A title
     * @return string
     */
    public static function slugify(string $string): string
    {
        return preg_replace('/\s+/', '-', mb_strtolower(trim(strip_tags($string)), 'UTF-8'));
    }
}
