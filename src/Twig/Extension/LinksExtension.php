<?php

/**
 * Twig Extension file
 *
 * @category    Internationalisation
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */

namespace App\Twig\Extension;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class LinksExtension extends AbstractExtension
{
    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * LinksExtension constructor.
     *
     * @param UrlGeneratorInterface $router Router injection
     */
    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    /**
     * Twig filter
     *
     * @return array
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('i18n_link', [$this, 'generateLink'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Twig function
     *
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('i18n_links', [$this, 'generateLinks'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Generate one link according to locale
     *
     * @param string $label
     * @param string $locale
     * @param string $routeName
     * @param array $parameters
     *
     * @return string
     */
    public function generateLink(string $label, string $locale, string $routeName, array $parameters): string
    {
        $url = $this->router->generate($routeName, array_merge($parameters, ['_locale' => $locale]));

        return sprintf('<a href="%s">%s</a>', $url, $label);
    }

    /**
     * Generate all links according to locales
     *
     * @param array $locales
     * @param string $routeName
     * @param array $routeParameters
     *
     * @return string
     */
    public function generateLinks(array $locales, string $routeName, array $routeParameters): string
    {
        $labels = ['en' => 'English', 'fr' => 'French'];
        $html = '';

        foreach ($locales as $locale) {
            $label = $labels[$locale];
            $html .= '<li>'.$this->generateLink($label, $locale, $routeName, $routeParameters).'</li>';
        }

        return $html;
    }
}
