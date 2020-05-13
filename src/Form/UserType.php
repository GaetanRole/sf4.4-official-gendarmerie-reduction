<?php

declare(strict_types=1);

namespace App\Form;

use App\Form\DataTransformer\ArrayToStringTransformer;
use App\Form\Type\AvatarType;
use App\Form\Type\ChangePasswordType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class UserType extends AbstractType
{
    /** @var AuthorizationCheckerInterface */
    protected $auth;

    /**
     * @see AuthorizationCheckerInterface To use isGranted() in buildForm
     */
    public function __construct(AuthorizationCheckerInterface $auth)
    {
        $this->auth = $auth;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'form.user.username.label',
                'help' => 'form.user.username.help',
                'attr' => ['placeholder' => 'form.user.username.placeholder', 'minLength' => '2', 'maxLength' => '64'],
            ])
            ->add('identity', TextType::class, [
                'label' => 'form.user.identity.label',
                'help' => 'form.user.identity.help',
                'attr' => ['placeholder' => 'form.user.identity.placeholder', 'minLength' => '2', 'maxLength' => '64'],
            ])
            ->add('email', EmailType::class, [
                'required' => false,
                'empty_data' => null,
                'label' => 'form.user.email.label',
                'help' => 'form.user.email.help',
                'attr' => ['placeholder' => 'form.user.email.placeholder', 'maxLength' => '64'],
            ])
            ->add('phoneNumber', TextType::class, [
                'required' => false,
                'label' => 'form.user.phone_number.label',
                'help' => 'form.user.phone_number.help',
                'attr' => ['placeholder' => 'form.user.phone_number.placeholder', 'maxLength' => '32'],
            ])
            ->add('avatar', AvatarType::class, [
                'expanded' => false,
                'multiple' => false,
                'label' => 'form.user.avatar.label',
                'help' => 'form.user.avatar.help',
            ])
            ->add('isActive', CheckboxType::class, [
                'required' => false,
                'label' => 'form.user.is_active.label',
                'help' => 'form.user.is_active.help',
            ])
        ;

        if ($this->auth->isGranted('ROLE_SUPER_ADMIN')) {
            $builder
                ->add('roles', ChoiceType::class, [
                    'expanded' => false,
                    'multiple' => false,
                    'empty_data' => 'ROLE_USER',
                    'choices' => [
                        'form.user.roles.choices.role_user' => 'ROLE_USER',
                        'form.user.roles.choices.role_admin' => 'ROLE_ADMIN',
                    ],
                    'label' => 'form.user.roles.label',
                    'help' => 'form.user.roles.help',
                ])
                ->get('roles')->addModelTransformer(new ArrayToStringTransformer(), true);
        }

        if (empty($options['data']->getPassword())) {
            $builder->add('plainPassword', ChangePasswordType::class, [
                'label' => false,
                'inherit_data' => true,
                'help' => 'form.user.plain_password.help',
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['translation_domain' => 'forms']);
    }
}
