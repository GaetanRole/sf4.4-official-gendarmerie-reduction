<?php

/**
 * ChangePassword FormType File
 *
 * PHP Version 7.2
 *
 * @category    User
 * @package     App\Form\Type
 * @version     1.0
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */

namespace App\Form\Type;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * ChangePassword FormType Class
 *
 * @category    User
 * @package     App\Form\Type
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
class ChangePasswordType extends AbstractType
{
    /**
     * Building form which can be inherited by UserType
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'plainPassword',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'constraints' => [
                        new NotBlank(['message' => 'form.change_password.plain_password.not_blank']),
                    ],
                    'invalid_message' => 'form.change_password.plain_password.invalid',
                    'options' => ['attr' => ['class' => 'password-field']],
                    'first_options'  => [
                        'label' => 'form.change_password.plain_password.first.label',
                        'attr' => ['placeholder' => 'form.change_password.plain_password.first.placeholder']],
                    'second_options' => [
                        'label' => 'form.change_password.plain_password.second.label',
                        'attr' => ['placeholder' => 'form.change_password.plain_password.second.placeholder']],
                ]
            )
        ;
    }

    /**
     * Set User class
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'translation_domain' => 'forms',
        ]);
    }
}
