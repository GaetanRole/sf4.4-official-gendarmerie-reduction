<?php

declare(strict_types=1);

namespace App\Consumer\GeoGouvApi\Model;

use App\Utils\AbstractBasicEnum;

/**
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
abstract class ModelEnum extends AbstractBasicEnum
{
    /** @var string Classes */
    public const DEPARTMENT_CLASS_NAME = 'Department';
    public const MUNICIPALITY_CLASS_NAME = 'Municipality';
    public const REGION_CLASS_NAME = 'Region';

    /** @var string Classes */
    public const REGION_INTERNATIONAL_ENDPOINT = 'International';
    public const REGION_NATIONAL_ENDPOINT = 'National';

    /** @var array Models */
    public const MODEL_CLASSES = [
        self::DEPARTMENT_CLASS_NAME,
        self::MUNICIPALITY_CLASS_NAME,
        self::REGION_CLASS_NAME,
    ];

    /** @var string Endpoints */
    public const DEPARTMENT_ENDPOINT = 'departements';
    public const MUNICIPALITY_ENDPOINT = 'communes';
    public const REGION_ENDPOINT = 'regions';
}
