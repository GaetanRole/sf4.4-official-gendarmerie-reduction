<?php

declare(strict_types=1);

namespace App\Consumer\GeoGouvApi\Model;

use App\Consumer\GeoGouvApi\GeoClient;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;

/**
 * @see     https://geo.api.gouv.fr/docs/communes
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
class Municipality extends GeoClient
{
    /** @var string */
    protected $url = '';

    /** @var array Available parameters for searching. */
    protected static $params = [
        'codePostal',
        'lon',
        'lat',
        'nom',
        'code',
        'codeDepartement',
        'codeRegion',
    ];

    /** @var array Available fields in return. */
    protected static $fields = [
        'code',
        'nom',
        'codesPostaux',
        'codeDepartement',
        'codeRegion',
        'population',
        'departement',
        'region',
        'surface',
        'centre',
        'contour',
    ];

    public function __construct(ClientInterface $httpClient)
    {
        $this->availableParams = self::$params;
        $this->availableFields = self::$fields;

        $this->url = parent::BASE_URI.ModelEnum::MUNICIPALITY_ENDPOINT;

        parent::__construct($httpClient);
    }

    /**
     * @throws GuzzleException
     */
    public function getAllMunicipalitiesByDepartment(string $department): array
    {
        $municipalities = [];
        foreach ($this->fields(['codesPostaux', 'nom'])->search('codeDepartement', $department) as $item) {
            $municipalities[implode(' / ', $item['codesPostaux']).' - '.$item['nom']] = $item['nom'];
        }

        return $municipalities;
    }
}
