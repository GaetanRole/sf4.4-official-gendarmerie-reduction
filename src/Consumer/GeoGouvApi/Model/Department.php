<?php

declare(strict_types=1);

namespace App\Consumer\GeoGouvApi\Model;

use App\Consumer\GeoGouvApi\GeoClient;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;

/**
 * @see     https://geo.api.gouv.fr/docs/departements
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
class Department extends GeoClient
{
    /** @var string */
    protected $url = '';

    /** @var array Available parameters for searching. */
    protected static $params = [
        'code',
        'codeRegion',
        'nom',
    ];

    /** @var array Available fields in return. */
    protected static $fields = [
        'code',
        'nom',
        'codeRegion',
        'region',
    ];

    public function __construct(ClientInterface $httpClient)
    {
        $this->availableParams = self::$params;
        $this->availableFields = self::$fields;

        $this->url = parent::BASE_URI.ModelEnum::DEPARTMENT_ENDPOINT;

        parent::__construct($httpClient);
    }

    /**
     * @throws GuzzleException
     */
    public function getAllDepartmentsByRegion(string $region): array
    {
        $departments = [];
        foreach ($this->fields(['code', 'nom'])->search('codeRegion', $region) as $item) {
            $departments[$item['code'].' - '.$item['nom']] = $item['code'];
        }

        return $departments;
    }
}
