<?php

declare(strict_types = 1);

namespace App\Tests\Controller;

use \Generator;
use App\Tests\InheritedWebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group   Functional
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class DefaultControllerTest extends InheritedWebTestCase
{
    /**
     * @dataProvider providePublicUrls
     */
    public function testPublicUrlsAreSuccessful(string $url): void
    {
        $this->webClient->followRedirects(false);

        $this->webClient->request('GET', $url);
        $this->assertSame(Response::HTTP_OK, $this->webClient->getResponse()->getStatusCode());
    }

    public function testUserDashboardMethodIsValidForAUserOrAAdminClient(): void
    {
        $this->webClient->followRedirects(false);

        // See credentials in src/DataFixtures/UserFixture or in sf_test_gendarmerie_reduction
        $this->webClient->setServerParameters([
            'PHP_AUTH_USER' => 'user0',
            'PHP_AUTH_PW'   => 'password0',
        ]);

        $this->webClient->request('GET', '/en/user/dashboard');
        $this->assertTrue(
            $this->webClient->getResponse()->isSuccessful() || $this->webClient->getResponse()->isRedirection()
        );
    }

    /**
     * This tests ensures that whenever a user tries to
     * access one of those pages, a redirection to the login form is performed.
     *
     * @dataProvider provideSecureUrls
     */
    public function testSecureUrlsRedirectingWellOnLoginForm(string $url): void
    {
        // Set to false because of mother class
        $this->webClient->followRedirects(false);

        $this->webClient->request('GET', $url);
        $response = $this->webClient->getResponse();
        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertSame('http://localhost/en/login', $response->getTargetUrl());
    }

    /**
     * _locale secured by LinksExtensionsTest.
     * Prepared test for later assertions.
     */
    public function providePublicUrls(): Generator
    {
        yield ['/en/'];
        yield ['/en/contact'];
    }

    /**
     * _locale secured by LinksExtensionsTest.
     */
    public function provideSecureUrls(): Generator
    {
        // Locale secured by LinksExtensionTest

        yield ['/en/user/dashboard'];
        yield ['/en/admin/dashboard'];
        yield ['/en/admin/brand/'];
        yield ['/en/admin/category/'];
        yield ['/en/admin/user/'];
        yield ['/en/reduction/'];
    }
}
