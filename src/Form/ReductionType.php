<?php

namespace App\Form;

use App\Api\GeoApiGouv\GeoClient;
use App\Entity\Category;
use App\Entity\Reduction;
use App\Entity\Brand;
use App\Form\EventListener\GeoApiFieldsSubscriber;
use App\Form\InheritedForm\UserIdentityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
class ReductionType extends AbstractType
{
    /** @var GeoClient */
    private $geoClient;

    /** @var RouterInterface */
    private $router;

    /**
     * @required
     */
    public function setGeoApiGouvClient(GeoClient $geoClient): ReductionType
    {
        $this->geoClient = $geoClient;
        return $this;
    }

    /**
     * @required
     */
    public function setRouter(RouterInterface $router): ReductionType
    {
        $this->router = $router;
        return $this;
    }

    /**
     * @todo Is CollectionType a better choice ?
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'userIdentity',
                UserIdentityType::class,
                [
                    'data_class' => Reduction::class,
                    'label' => false,
                ]
            )
            ->add(
                'brand',
                EntityType::class,
                [
                    'label' => 'form.reduction.brand.label',
                    'help' => 'form.reduction.brand.help',
                    'class' => Brand::class,
                    'choice_label' => 'name',
                    'required' => true,
                    'expanded' => false,
                    'multiple' => false,
                ]
            )
            ->add(
                'title',
                TextType::class,
                [
                    'label' => 'form.reduction.title.label',
                    'help' => 'form.reduction.title.help',
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'form.reduction.title.placeholder',
                        'minLength' => '5',
                        'maxLength' => '64',
                    ],
                ]
            )
            ->add(
                'description',
                TextareaType::class,
                [
                    'required' => true,
                    'label' => 'form.reduction.description.label',
                    'help' => 'form.reduction.description.help',
                    'attr' => [
                        'placeholder' => 'form.reduction.description.placeholder',
                        'minLength' => '10',
                        'maxLength' => '10000',
                        'rows' => 50,
                    ],
                ]
            )
            ->add(
                'categories',
                EntityType::class,
                [
                    'required' => true,
                    'class' => Category::class,
                    'choice_label' => 'name',
                    'label' => 'form.reduction.categories.label',
                    'help' => 'form.reduction.categories.help',
                    'query_builder' => static function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                            ->orderBy('c.name', 'ASC');
                    },
                    'expanded'  => false,
                    'multiple'  => true,
                ]
            )
        ;
        $builder->addEventSubscriber(new GeoApiFieldsSubscriber($this->router, $this->geoClient));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reduction::class,
            'translation_domain' => 'forms',
        ]);
    }
}
