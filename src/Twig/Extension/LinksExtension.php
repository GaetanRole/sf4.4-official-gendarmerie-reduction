<?php

declare(strict_types = 1);

namespace App\Twig\Extension;

use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class LinksExtension extends AbstractExtension
{
    /** @var array CONST for locale labels, useful to create Twig links. */
    public const LOCALE_EN_LABELS = ['en' => 'English', 'fr' => 'French'];
    public const LOCALE_FR_LABELS = ['en' => 'Englais', 'fr' => 'Français'];

    /** @var UrlGeneratorInterface */
    private $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    public function getFilters(): array
    {
        return [new TwigFilter('i18n_link', [$this, 'generateLink'], ['is_safe' => ['html']])];
    }

    public function getFunctions(): array
    {
        return [new TwigFunction('i18n_links', [$this, 'generateLinks'], ['is_safe' => ['html']])];
    }

    public function generateLink(string $label, string $locale, string $routeName, array $parameters): string
    {
        $url = $this->router->generate($routeName, array_merge($parameters, ['_locale' => $locale]));
        return sprintf('<a href="%s" class="dropdown-item">%s</a>', $url, $label);
    }

    public function generateLinks(array $locales, string $routeName, array $routeParameters, string $country): string
    {
        $html = '<ul>';

        foreach ($locales as $locale) {
            $label = ($country === 'fr') ? self::LOCALE_FR_LABELS[$locale] : self::LOCALE_EN_LABELS[$locale];
            $html .= '<li>'.$this->generateLink($label, $locale, $routeName, $routeParameters).'</li>';
        }

        return $html . '</ul>';
    }
}
