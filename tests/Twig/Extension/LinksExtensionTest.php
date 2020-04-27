<?php

declare(strict_types=1);

namespace App\Tests\Twig\Extension;

use Twig\TwigFilter;
use Twig\TwigFunction;
use PHPUnit\Framework\TestCase;
use Twig\Extension\AbstractExtension;
use App\Twig\Extension\LinksExtension;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @group   Unit
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class LinksExtensionTest extends TestCase
{
    /** @var LinksExtension */
    private $linksExtension;

    protected function setUp(): void
    {
        $urlGeneratorMock = $this->createMock(UrlGeneratorInterface::class);

        $urlGeneratorMock->expects($this->atMost(2))
            ->method('generate')
            ->withConsecutive(['app_index', ['_locale' => 'en']], ['app_index', ['_locale' => 'fr']])
            ->willReturnOnConsecutiveCalls('/en/login', '/fr/login');

        $this->linksExtension = new LinksExtension($urlGeneratorMock);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->linksExtension = null;
    }

    public function testLinksExtensionExtendingAbstractExtension(): void
    {
        $this->assertInstanceOf(AbstractExtension::class, $this->linksExtension);
    }

    public function testGetFiltersReturningAnArrayContainingOnlyTwigFilters(): void
    {
        $this->assertContainsOnlyInstancesOf(TwigFilter::class, $this->linksExtension->getFilters());
    }

    public function testGetFunctionsReturningAnArrayContainingOnlyTwigFunctions(): void
    {
        $this->assertContainsOnlyInstancesOf(TwigFunction::class, $this->linksExtension->getFunctions());
    }

    public function testGenerateLinkWithOneValidLocaleAndRoute(): void
    {
        $this->assertSame(
            '<a href="/en/login" class="dropdown-item">English</a>',
            $this->linksExtension->generateLink('English', 'en', 'app_index', [])
        );
    }

    public function testGenerateLinksWithFewValidLocales(): void
    {
        $enExpectedValue = '<li><a href="/en/login" class="dropdown-item">English</a></li>';
        $frExpectedValue = '<li><a href="/fr/login" class="dropdown-item">French</a></li>';

        $this->assertSame(
            '<ul>'.$enExpectedValue.$frExpectedValue.'</ul>',
            $this->linksExtension->generateLinks(['en', 'fr'], 'app_index', [], 'en')
        );
    }
}
