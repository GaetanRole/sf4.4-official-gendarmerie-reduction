<?php

/**
 * UserIdentity FormType File
 *
 * @category    UserIdentityType
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */

namespace App\Form\InheritedForm;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * UserIdentity FormType Class used by OpinionType and ReductionType
 */
class UserIdentityType extends AbstractType
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
                    'required' => false,
                    'empty_data' => null,
                    'label' => 'form.user_identity.name.label',
                    'help' => 'form.user_identity.name.help',
                    'attr' => [
                        'placeholder' => 'form.user_identity.name.placeholder',
                        'maxLength' => '64',
                    ],
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'required' => false,
                    'empty_data' => null,
                    'label' => 'form.user_identity.email.label',
                    'help' => 'form.user_identity.email.help',
                    'attr' => [
                        'placeholder' => 'form.user_identity.email.placeholder',
                        'maxLength' => '64',
                    ],
                ]
            )
        ;
    }

    /**
     * Set inherit_data
     *
     * @see https://symfony.com/doc/master/form/inherit_data_option.html
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'inherit_data' => true,
            'translation_domain' => 'forms',
        ]);
    }
}
