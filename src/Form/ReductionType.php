<?php

declare(strict_types=1);

namespace App\Form;

use App\Consumer\GeoGouvApi\GeoClient;
use App\Entity\Brand;
use App\Entity\Category;
use App\Entity\Reduction;
use App\Form\EventSubscriber\GeoApiFieldsSubscriber;
use App\Form\InheritedForm\UserIdentityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class ReductionType extends AbstractType
{
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
        $builder
            ->add('userIdentity', UserIdentityType::class, [
                'data_class' => Reduction::class,
                'label' => false,
            ])
            ->add('brand', EntityType::class, [
                'expanded' => false,
                'multiple' => false,
                'class' => Brand::class,
                'placeholder' => 'form.reduction.brand.placeholder',
                'choice_label' => 'name',
                'attr' => ['data-select' => true],
                'label' => 'form.reduction.brand.label',
                'help' => 'form.reduction.brand.help',
            ])
            ->add('title', TextType::class, [
                'label' => 'form.reduction.title.label',
                'help' => 'form.reduction.title.help',
                'attr' => [
                    'placeholder' => 'form.reduction.title.placeholder',
                    'minLength' => '5',
                    'maxLength' => '64',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'form.reduction.description.label',
                'help' => 'form.reduction.description.help',
                'attr' => [
                    'placeholder' => 'form.reduction.description.placeholder',
                    'minLength' => '10',
                    'maxLength' => '1024',
                    'rows' => 10,
                ],
            ])
            ->add('image', ImageType::class, [
                'required' => false,
                'label' => 'form.reduction.image.label',
                'help' => 'form.reduction.image.help',
            ])
            ->add('categories', EntityType::class, [
                'expanded' => false,
                'multiple' => true,
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'form.reduction.categories.label',
                'help' => 'form.reduction.categories.help',
                'query_builder' => static function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC')
                    ;
                },
            ])
        ;

        $builder->addEventSubscriber(new GeoApiFieldsSubscriber($this->router, $this->geoClient));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['translation_domain' => 'forms']);
    }
}
