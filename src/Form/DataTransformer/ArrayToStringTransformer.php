<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * @see     DataTransformerInterface
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class ArrayToStringTransformer implements DataTransformerInterface
{
    public const DELIMITER = ', ';

    /**
     * Transforms an array to a string.
     *
     * @param   mixed $array The value in the original representation
     * @return  string|null The value in the transformed representation
     */
    public function transform($array): ?string
    {
        return (null === $array || empty($array)) ? '' : implode(self::DELIMITER, $array);
    }

    /**
     * Reverse transform a string to an array.
     *
     * @param   mixed $string The value in the transformed representation
     * @return  array|null The value in the original representation
     */
    public function reverseTransform($string): ?array
    {
        return ('' === $string || null === $string) ? [] : explode(self::DELIMITER, $string);
    }
}
