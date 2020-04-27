<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Opinion;
use App\Form\InheritedForm\UserIdentityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class OpinionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('userIdentity', UserIdentityType::class, ['data_class' => Opinion::class])
            ->add('comment', TextareaType::class, [
                'required' => true,
                'label' => 'form.opinion.comment.label',
                'help' => 'form.opinion.comment.help',
                'attr' => [
                    'placeholder' => 'form.opinion.comment.placeholder',
                    'minLength' => '5',
                    'maxLength' => '10000',
                    'rows' => 30,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Opinion::class, 'translation_domain' => 'forms']);
    }
}
