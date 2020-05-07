<?php

declare(strict_types=1);

namespace App\Form;

use App\Consumer\GeoGouvApi\GeoClient;
use App\Form\EventSubscriber\GeoApiFieldsSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class SearchType extends AbstractType
{
    /** @var array */
    public const SEARCH_METHODS = ['location', 'brand'];

    /** @var GeoClient */
    private $geoClient;

    /** @var RouterInterface */
    private $router;

    /**
     * @see GeoClient To get departments and municipalities autocompletion
     * @see RouterInterface To get API data-autocomplete-url for GeoApiFieldsSubscriber
     */
    public function __construct(GeoClient $geoClient, RouterInterface $router)
    {
        $this->geoClient = $geoClient;
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('method', ChoiceType::class, [
            'choices' => [
                'form.search.method.label.location' => self::SEARCH_METHODS[0],
                'form.search.method.label.brand' => self::SEARCH_METHODS[1],
            ],
            'data' => 'location',
            'expanded' => true,
            'multiple' => false,
        ]);

        $builder->addEventSubscriber(new GeoApiFieldsSubscriber($this->router, $this->geoClient));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'forms',
            'attr' => ['id' => 'search-form'],
        ]);
    }
}
