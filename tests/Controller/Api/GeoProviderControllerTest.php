<?php

declare(strict_types=1);

namespace App\Tests\Controller\Api;

use \Generator;
use App\Tests\AbstractWebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group   Functional
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class GeoProviderControllerTest extends AbstractWebTestCase
{
    /**
     * @dataProvider provideApiUrls
     */
    public function testApiUrlsAreSuccessfulWithValidGivenQueryParameters(string $url): void
    {
        self::$webClient->request('GET', $url);
        self::assertResponseIsSuccessful();
    }

    public function testAnApiUrlWithoutParametersToGetAnErrorCode(): void
    {
        self::$webClient->request('GET', '/en/api/geo/get-municipalities-from-department');
        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * _locale secured by LinksExtensionsTest.
     */
    public function provideApiUrls(): Generator
    {
        yield ['/en/api/geo/get-municipalities-from-department?search=Nord'];
        yield ['/en/api/geo/get-departments-from-region?search=National'];
    }
}
