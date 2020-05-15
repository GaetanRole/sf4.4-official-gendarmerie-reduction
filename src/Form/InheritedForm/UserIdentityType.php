<?php

declare(strict_types=1);

namespace App\Form\InheritedForm;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * UserIdentity FormType Class used by OpinionType and ReductionType.
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class UserIdentityType extends AbstractType
{
    /** @var User $user */
    private $user = null;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->user = $tokenStorage->getToken() ? $tokenStorage->getToken()->getUser() : null;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $userName = null;
        $userEmail = null;

        $this->setUserIdentityByRef($userName, $userEmail);

        $builder
            ->add('name', TextType::class, [
                'required' => false,
                'data' => $userName,
                'label' => 'form.user_identity.name.label',
                'help' => 'form.user_identity.name.help',
                'attr' => ['placeholder' => 'form.user_identity.name.placeholder', 'maxLength' => '64'],
            ])
            ->add('email', EmailType::class, [
                'required' => false,
                'data' => $userEmail,
                'label' => 'form.user_identity.email.label',
                'help' => 'form.user_identity.email.help',
                'attr' => ['placeholder' => 'form.user_identity.email.placeholder', 'maxLength' => '64'],
            ])
        ;
    }

    /**
     * Set inherit_data.
     *
     * @see https://symfony.com/doc/master/form/inherit_data_option.html
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['inherit_data' => true, 'translation_domain' => 'forms']);
    }

    /**
     * Set User name and email by ref.
     */
    private function setUserIdentityByRef(?string &$userName, ?string &$userEmail): void
    {
        if ($this->user && $this->user->isAdmin()) {
            $userName = $this->user->getUsername();
            $userEmail = $this->user->getEmail();
        }
    }
}
