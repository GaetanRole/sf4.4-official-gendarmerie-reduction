<?php

namespace App\Api\GeoApiGouv\Model;

use App\Api\GeoApiGouv\GeoClient;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * @see     https://geo.api.gouv.fr/docs/departements
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
class Department extends GeoClient
{
    public const ENDPOINT = 'departements';

    protected $url = '';

    /** Available parameters for searching. */
    protected static $params = [
        'code',
        'codeRegion',
        'nom'
    ];

    /** Available fields in return. */
    protected static $fields = [
        'code',
        'nom',
        'codeRegion',
        'region'
    ];

    public function __construct(Client $httpClient)
    {
        parent::__construct($httpClient);

        $this->availableParams = self::$params;
        $this->availableFields = self::$fields;

        $this->url = parent::BASE_URI . self::ENDPOINT;
    }

    /**
     * @throws GuzzleException
     */
    public function getAllDepartmentsByRegion(string $region): array
    {
        $departments = [];
        foreach ($this->fields(['code', 'nom'])->search('codeRegion', $region) as $key => $item) {
            $departments[$item['code'].' - '.$item['nom']] = $item['code'];
        }

        return $departments;
    }
}
