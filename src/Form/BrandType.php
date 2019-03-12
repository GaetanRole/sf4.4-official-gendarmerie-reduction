<?php

/**
 * Brand FormType File
 *
 * PHP Version 7.2
 *
 * @category    Brand
 * @package     App\Form
 * @version     1.0
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */

namespace App\Form;

use App\Entity\Brand;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Brand FormType Class
 *
 * @category    Brand
 * @package     App\Form
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
class BrandType extends AbstractType
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
                    'label' => 'Nom de l\'enseigne *',
                    'help' => 'Pensez à bien vérifier l\'intégrité de votre nom d\'enseigne.',
                    'attr' => [
                        'placeholder' => 'Indiquez une enseigne. Ex: McDonald\'s.',
                        'minLength' => '2',
                        'maxLength' => '128',
                    ],
                ]
            )
            ->add(
                'description',
                TextareaType::class,
                [
                    'required' => false,
                    'label' => 'Description de l\'enseigne',
                    'empty_data' => 'Aucune description renseignée.',
                    'help' => 'Description non obligatoire, mais pouvant expliciter les services d\'une enseigne.',
                    'attr' => [
                        'placeholder' => 'Renseignez ici une éventuelle description.',
                        'maxLength' => '255',
                        'rows' => 20,
                    ],
                ]
            )
        ;
    }

    /**
     * Set Brand class
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Brand::class,
        ]);
    }
}
