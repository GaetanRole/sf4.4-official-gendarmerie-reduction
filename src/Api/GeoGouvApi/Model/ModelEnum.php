<?php

declare(strict_types = 1);

namespace App\Api\GeoGouvApi\Model;

use App\Utils\AbstractBasicEnum;

/**
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
abstract class ModelEnum extends AbstractBasicEnum
{
    /** @var string Classes */
    public const DEPARTMENT_CLASS_NAME = 'Department',
        MUNICIPALITY_CLASS_NAME = 'Municipality',
        REGION_CLASS_NAME = 'Region';

    /** @var string Classes */
    public const REGION_INTERNATIONAL_ENDPOINT = 'International',
        REGION_NATIONAL_ENDPOINT = 'National';

    /** @var array Models */
    public const MODEL_CLASSES = [
        self::DEPARTMENT_CLASS_NAME,
        self::MUNICIPALITY_CLASS_NAME,
        self::REGION_CLASS_NAME,
    ];

    /** @var string Endpoints */
    public const DEPARTMENT_ENDPOINT = 'departements',
        MUNICIPALITY_ENDPOINT = 'communes',
        REGION_ENDPOINT = 'regions';
}
