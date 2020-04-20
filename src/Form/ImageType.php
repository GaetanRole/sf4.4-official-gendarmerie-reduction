<?php

declare(strict_types = 1);

namespace App\Form;

use App\Entity\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

/**
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('file', FileType::class, [
            'label' => false,
            'attr' => [
                'placeholder' => 'form.image.file.placeholder'
            ]
        ]);

        if (true === $options['allow_delete']) {
            $builder->add('deleted', CheckboxType::class, [
                'label' => 'form.image.delete.label',
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => Image::class,
                'allow_delete' => false,
                'translation_domain' => 'forms',
            ])
            ->setAllowedTypes('allow_delete', 'bool')
        ;
    }
}
