<?php

declare(strict_types=1);

namespace App\Consumer\GeoGouvApi;

use App\Consumer\GeoGouvApi\Model\ModelEnum;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpFoundation\Response;
use \InvalidArgumentException;

/**
 * Class GeoClient consuming https://geo.api.gouv.fr/ API.
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
class GeoClient
{
    public const BASE_URI = 'https://geo.api.gouv.fr/';
    public const MODEL_API_NAMESPACE = 'App\Consumer\GeoGouvApi\Model\\';

    /** @var string */
    protected $url = self::BASE_URI;

    /** @var string */
    protected $userParam = '';
    /** @var string */
    protected $userSearch = '';
    /** @var array */
    protected $userFields = [];

    /** @var array */
    protected $availableParams = [];
    /** @var array */
    protected $availableFields = [];

    /** @var Client */
    protected $httpClient;

    /** @param  ClientInterface $httpClient A Guzzle client which will be replaced by Symfony\HttpClient. */
    public function __construct(ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function __call(string $className, array $arguments)
    {
        if (in_array($className, ModelEnum::MODEL_CLASSES, true)) {
            $className = self::MODEL_API_NAMESPACE.$className;
            return new $className($this->httpClient);
        }

        throw new InvalidArgumentException('Class: '.self::MODEL_API_NAMESPACE.$className.' doesn\'t exist.');
    }

    public function fields(array $fields): GeoClient
    {
        foreach ($fields as $field) {
            if (in_array($field, $this->availableFields, false)) {
                $this->userFields[] = $field;
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
            $this->userParam = $key;
            $this->userSearch = $value;
        }

        $url = $this->url.'?'.$this->userParam.'='.$this->userSearch;

        if (count($this->userFields) > 0) {
            $url .= '&fields='.implode(',', $this->userFields);
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
            return json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        }

        return [$response->getStatusCode() => $response->getReasonPhrase()];
    }
}
