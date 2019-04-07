<?php

/**
 * Brand FormType File
 *
 * @category    Brand
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */

namespace App\Form;

use App\Entity\Brand;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
                    'label' => 'form.brand.name.label',
                    'help' => 'form.brand.name.help',
                    'attr' => [
                        'placeholder' => 'form.brand.name.placeholder',
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
                    'label' => 'form.brand.description.label',
                    'empty_data' => null,
                    'help' => 'form.brand.description.help',
                    'attr' => [
                        'placeholder' => 'form.brand.description.placeholder',
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
            'translation_domain' => 'forms',
        ]);
    }
}
