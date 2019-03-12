<?php

/**
 * Category FormType File
 *
 * PHP Version 7.2
 *
 * @category    Category
 * @package     App\Form
 * @version     1.0
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Category FormType Class
 *
 * @category    Category
 * @package     App\Form
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
class CategoryType extends AbstractType
{
    /**
     * Building form
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'required' => true,
                    'label' => 'Nom de la catégorie *',
                    'help' => 'Pensez à bien vérifier l\'intégrité de votre nom de catégorie.',
                    'attr' => [
                        'placeholder' => 'Indiquez une catégorie. Ex: Restauration.',
                        'minLength' => '2',
                        'maxLength' => '64',
                    ],
                ]
            )
            ->add(
                'description',
                TextareaType::class,
                [
                    'required' => false,
                    'label' => 'Description de la catégorie',
                    'empty_data' => 'Aucune description renseignée.',
                    'help' => 'Description non obligatoire, mais pouvant expliciter votre catégorie.',
                    'attr' => [
                        'placeholder' => 'Renseignez une éventuelle description.',
                        'maxLength' => '255',
                        'rows' => 20,
                    ],
                ]
            )
        ;
    }

    /**
     * Set Category class
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
