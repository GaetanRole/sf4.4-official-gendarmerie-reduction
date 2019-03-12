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
                    'data_class' => Reduction::class
                ]
            )
            ->add(
                'brand',
                EntityType::class,
                [
                    'label' => 'Choisissez l\'enseigne proposant une réduction *',
                    'help' => 'Retrouvez et sélectionnez une enseigne déjà existante
                    ou proposez en une nouvelle, qui sera ajoutée une fois l\'annonce validée.',
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
                    'label' => 'Titre de votre réduction (montant inclus) *',
                    'help' => 'Les titres non conformes à notre code de conduite seront modérés
                    et la réduction non acceptée. Veuillez précisez le montant de la réduction dans le titre.',
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'Ex: McDonald\'s -15% durant le weekend.',
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
                    'label' => 'Votre description *',
                    'help' => 'Les descriptions non conformes à notre code de conduite seront modérées
                    et la réduction non acceptée.',
                    'attr' => [
                        'placeholder' => 'Ex: Réduction de 15%, uniquement avec la carte pro !',
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
                    'label' => 'Sélectionnez votre département *',
                    'help' => 'Votre réduction sera filtrable par département une fois celle-ci validée par les
                    administrateurs.',
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
                    'label' => 'Renseignez votre ville *',
                    'help' => 'Votre réduction sera filtrable par ville une fois celle-ci validée par les
                    administrateurs.',
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
                    'label' => 'Ajoutez les différentes catégories applicables à votre réduction *',
                    'help' => 'Vous pouvez ajouter entre une et trois catégories.',
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
        ]);
    }
}
