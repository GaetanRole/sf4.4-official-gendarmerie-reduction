<?php

/**
 * ArrayToStringTransformer File
 *
 * PHP Version 7.2
 *
 * @category    DataTransformer
 * @package     App\Form\DataTransformer
 * @version     1.0
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * ArrayToStringTransformer Class
 *
 * @category    DataTransformer
 * @package     App\Form\DataTransformer
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
class ArrayToStringTransformer implements DataTransformerInterface
{
    /**
     * Transforms an array to a string
     *
     * @param mixed $array
     * @return string
     */
    public function transform($array): ?string
    {
        if (empty($array) || null === $array) {
            return '';
        }

        return implode(', ', $array);
    }

    /**
     * Reverse transform a string to an array
     *
     * @param mixed $string
     * @return array|null
     */
    public function reverseTransform($string): ?array
    {
        if ('' === $string || null === $string) {
            return [];
        }

        return explode(', ', $string);
    }
}
