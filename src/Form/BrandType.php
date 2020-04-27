<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Brand;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class BrandType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'form.brand.name.label',
                'help' => 'form.brand.name.help',
                'attr' => [
                    'placeholder' => 'form.brand.name.placeholder',
                    'minLength' => '2',
                    'maxLength' => '64',
                ],
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'empty_data' => null,
                'label' => 'form.brand.description.label',
                'help' => 'form.brand.description.help',
                'attr' => [
                    'placeholder' => 'form.brand.description.placeholder',
                    'maxLength' => '255',
                    'rows' => 20,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Brand::class, 'translation_domain' => 'forms']);
    }
}
