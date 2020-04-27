<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Entity\Image;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\ImageValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
class ImageObjectValidator extends ImageValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ImageObject) {
            throw new UnexpectedTypeException($constraint, ImageObject::class);
        }

        if (null === $value) {
            return;
        }

        if (!$value instanceof Image) {
            throw new UnexpectedTypeException($value, Image::class);
        }

        parent::validate($value->getFile(), $constraint);
    }
}
