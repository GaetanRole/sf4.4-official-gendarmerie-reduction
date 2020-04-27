<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'form.category.name.label',
                'help' => 'form.category.name.help',
                'attr' => [
                    'placeholder' => 'form.category.name.placeholder',
                    'minLength' => '2',
                    'maxLength' => '64',
                ],
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'empty_data' => null,
                'label' => 'form.category.description.label',
                'help' => 'form.category.description.help',
                'attr' => [
                    'placeholder' => 'form.category.description.placeholder',
                    'maxLength' => '255',
                    'rows' => 20,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Category::class, 'translation_domain' => 'forms']);
    }
}
