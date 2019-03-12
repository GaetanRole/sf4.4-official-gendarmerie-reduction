<?php

/**
 * Slugger util file
 *
 * PHP Version 7.2
 *
 * @category    Slugger
 * @package     App\Utils
 * @version     1.0
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */

namespace App\Utils;

/**
 * Slugger util class.
 *
 * @category    Slugger
 * @package     App\Utils
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
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
