<?php

declare(strict_types=1);

namespace App\Consumer\GeoGouvApi\Model;

use App\Consumer\GeoGouvApi\GeoClient;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;

/**
 * @see     https://geo.api.gouv.fr/docs/regions
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
class Region extends GeoClient
{
    /** @var string */
    protected $url = '';

    /** @var array Available parameters for searching. */
    protected static $params = [
        'code',
        'nom',
    ];

    /** @var array Available fields in return. */
    protected static $fields = [
        'code',
        'nom',
        'codeDepartement',
    ];

    public function __construct(ClientInterface $httpClient)
    {
        $this->availableParams = self::$params;
        $this->availableFields = self::$fields;

        $this->url = parent::BASE_URI.ModelEnum::REGION_ENDPOINT;

        parent::__construct($httpClient);
    }

    /**
     * @throws GuzzleException
     */
    public function getAllRegions(): array
    {
        $regions = [];

        // Hardcoded values for international and national reductions
        // Added in Regex constraint into Reduction entity
        $regions[ModelEnum::REGION_INTERNATIONAL_ENDPOINT] = ModelEnum::REGION_INTERNATIONAL_ENDPOINT;
        $regions[ModelEnum::REGION_NATIONAL_ENDPOINT] = ModelEnum::REGION_NATIONAL_ENDPOINT;

        foreach ($this->fields(['code', 'nom'])->search() as $item) {
            $regions[$item['nom']] = $item['code'];
        }

        return $regions;
    }
}
