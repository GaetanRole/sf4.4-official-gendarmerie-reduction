<?php

/**
 * Reduction FormType File
 *
 * PHP Version 7.2
 *
 * @category    Reduction
 * @package     App\Form
 * @version     1.0
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */

namespace App\Form;

use App\Entity\Category;
use App\Entity\Reduction;
use App\Entity\Brand;
use App\Form\InheritedForm\UserIdentityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Reduction FormType Class
 *
 * @category    Reduction
 * @package     App\Form
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
class ReductionType extends AbstractType
{
    /**
     * Building form
     *
     * @todo Is CollectionType a better choice ?
     *
     * @param FormBuilderInterface $builder
     * @param array $options
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
                'department',
                TextType::class,
                [
                    'required' => true,
                    'label' => 'form.reduction.department.label',
                    'help' => 'form.reduction.department.help',
                    'attr' => [
                        'minLength' => '3',
                        'maxLength' => '32',
                    ],
                ]
            )
            ->add(
                'city',
                TextType::class,
                [
                    'required' => true,
                    'label' => 'form.reduction.city.label',
                    'help' => 'form.reduction.city.help',
                    'attr' => [
                        'minLength' => '2',
                        'maxLength' => '64',
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
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                            ->orderBy('c.name', 'ASC');
                    },
                    'expanded'  => false,
                    'multiple'  => true,
                ]
            )
        ;
    }

    /**
     * Set Reduction class
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reduction::class,
            'translation_domain' => 'forms',
        ]);
    }
}
