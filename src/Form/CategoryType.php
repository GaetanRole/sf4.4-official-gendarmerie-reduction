<?php

/**
 * Category FormType File
 *
 * @category    Category
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
                    'label' => 'form.category.name.label',
                    'help' => 'form.category.name.help',
                    'attr' => [
                        'placeholder' => 'form.category.name.placeholder',
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
                    'label' => 'form.category.description.label',
                    'empty_data' => null,
                    'help' => 'form.category.description.help',
                    'attr' => [
                        'placeholder' => 'form.category.description.placeholder',
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
            'translation_domain' => 'forms',
        ]);
    }
}
