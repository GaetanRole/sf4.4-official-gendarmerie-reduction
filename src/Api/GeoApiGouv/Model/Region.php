<?php

namespace App\Api\GeoApiGouv\Model;

use App\Api\GeoApiGouv\GeoClient;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * @see     https://geo.api.gouv.fr/docs/regions
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
class Region extends GeoClient
{
    public const ENDPOINT = 'regions';

    protected $url = '';

    /** Available parameters for searching. */
    protected static $params = [
        'code',
        'nom'
    ];

    /** Available fields in return. */
    protected static $fields = [
        'code',
        'nom',
        'codeDepartement'
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
    public function getAllRegions(): array
    {
        $regions = [];
        foreach ($this->fields(['code', 'nom'])->search() as $key => $item) {
            $regions[$item['nom']] = $item['code'];
        }

        return $regions;
    }
}
