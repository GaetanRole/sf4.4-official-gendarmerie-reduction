<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use \Generator;
use App\Tests\AbstractWebTestCase;

/**
 * @group   Functional
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class SmokeTest extends AbstractWebTestCase
{
    /**
     * @dataProvider providePublicUrls
     */
    public function testAllPublicUrlsAreSuccessful(string $url): void
    {
        self::$webClient->request('GET', $url);
        self::assertResponseIsSuccessful();
    }

    /**
     * This test ensures that whenever a user tries to
     * access one of those pages, a redirection to the login form is performed.
     *
     * @dataProvider provideSecureUrls
     */
    public function testSecureUrlsRedirectingWellOnLoginForm(string $url): void
    {
        self::$webClient->request('GET', $url);
        self::assertResponseRedirects();
        self::assertSame('/en/login', self::$webClient->getResponse()->getTargetUrl());
    }

    /**
     * This test ensures that whenever a user tries to
     * access one of those pages, responses are successful for User role.
     *
     * @dataProvider provideUserUrls
     */
    public function testUserRoutesAreValidForAnUserRole(string $url): void
    {
        self::$webClient->setServerParameters([
            'PHP_AUTH_USER' => 'user0',
            'PHP_AUTH_PW'   => 'password0',
        ]);

        self::$webClient->request('GET', $url);
        self::assertResponseIsSuccessful();
    }

    /**
     * _locale secured by LinksExtensionsTest.
     * Prepared test for later assertions.
     */
    public function providePublicUrls(): Generator
    {
        yield ['/en/'];
        yield ['/en/contact'];
        yield ['/en/login'];
        yield ['/en/api/geo/get-municipalities-from-department?search=Nord'];
        yield ['/en/api/geo/get-departments-from-region?search=National'];
    }

    /**
     * _locale secured by LinksExtensionsTest.
     */
    public function provideSecureUrls(): Generator
    {
        yield ['/en/admin/dashboard'];
        yield ['/en/admin/brand/'];
        yield ['/en/admin/category/'];
        yield ['/en/admin/user/'];
        yield ['/en/admin/reduction/waiting-list'];
        yield ['/en/reduction/'];
        yield ['/en/reduction/post'];
    }

    /**
     * _locale secured by LinksExtensionsTest.
     */
    public function provideUserUrls(): Generator
    {
        yield ['/en/reduction/'];
        yield ['/en/reduction/post'];
    }
}
