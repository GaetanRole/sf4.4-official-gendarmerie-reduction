<?php

namespace App\Api\GeoApiGouv\Model;

use App\Api\GeoApiGouv\GeoClient;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * @see     https://geo.api.gouv.fr/docs/communes
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
class Municipality extends GeoClient
{
    public const ENDPOINT = 'communes';

    protected $url = '';

    /** Available parameters for searching. */
    protected static $params = [
        'codePostal',
        'lon',
        'lat',
        'nom',
        'code',
        'codeDepartement',
        'codeRegion'
    ];

    /** Available fields in return. */
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
        'contour'
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
    public function getAllMunicipalitiesByDepartment(string $department): array
    {
        $municipalities = [];
        foreach ($this->fields(['codesPostaux', 'nom'])->search('codeDepartement', $department) as $key => $item) {
            $municipalities[implode(' / ', $item['codesPostaux']) . ' - ' . $item['nom']] = $item['nom'];
        }

        return $municipalities;
    }
}
