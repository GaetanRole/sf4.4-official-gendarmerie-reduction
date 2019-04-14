<?php

namespace App\Api\GeoApiGouv;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpFoundation\Response;
use InvalidArgumentException;

/**
 * Class GeoClient consuming https://geo.api.gouv.fr/ API.
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
class GeoClient
{
    public const BASE_URI = 'https://geo.api.gouv.fr/';
    public const MODEL_API_NAMESPACE = 'App\Api\GeoApiGouv\Model\\';

    protected $url = self::BASE_URI;

    protected $user_param = '';
    protected $user_search = '';
    protected $user_fields = [];

    protected $availableParams = [];
    protected $availableFields = [];

    /** @var Client */
    protected $httpClient;

    /** @param Client $httpClient A Guzzle client which will be replaced by Symfony\HttpClient. */
    public function __construct(Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function __call(string $className, array $arguments)
    {
        if (in_array($className, ['Municipality', 'Department', 'Region'])) {
            $className = self::MODEL_API_NAMESPACE . $className;
            return new $className($this->httpClient);
        }

        throw new InvalidArgumentException('Class : ' . self::MODEL_API_NAMESPACE . $className . ' doesn\'t exist.');
    }

    public function fields(array $fields): GeoClient
    {
        foreach ($fields as $field) {
            if (in_array($field, $this->availableFields, false)) {
                $this->user_fields[] = $field;
            }
        }

        return $this;
    }

    /**
     * @throws GuzzleException
     */
    public function search(string $key = '', string $value = ''): array
    {
        if (in_array($key, $this->availableParams, false)) {
            $this->user_param = $key;
            $this->user_search = $value;
        }

        $url = $this->url . '?' . $this->user_param . '=' . $this->user_search;

        if (count($this->user_fields) > 0) {
            $url .= '&fields=' . implode(',', $this->user_fields);
        }

        return $this->doRequest('GET', $url);
    }

    /**
     * @throws GuzzleException
     */
    private function doRequest(string $method, string $url): array
    {
        try {
            $response = $this->httpClient->request($method, $url);
        } catch (ClientException $e) {
            return [$e->getCode() => $e->getMessage()];
        }

        if (Response::HTTP_OK === $response->getStatusCode()) {
            return json_decode($response->getBody()->getContents(), true);
        }

        return [$response->getStatusCode() => $response->getReasonPhrase()];
    }
}