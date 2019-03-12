<?php

/**
 * User FormType File
 *
 * PHP Version 7.2
 *
 * @category    User
 * @package     App\Form
 * @version     1.0
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */

namespace App\Form;

use App\Entity\User;
use App\Form\DataTransformer\ArrayToStringTransformer;
use App\Form\Type\ChangePasswordType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * User FormType Class
 *
 * @category    User
 * @package     App\Form
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
class UserType extends AbstractType
{
    /**
     * @var AuthorizationCheckerInterface
     */
    protected $auth;

    /**
     * UserType constructor.
     *
     * @see To use isGranted() in buildForm
     * @param AuthorizationCheckerInterface $auth
     */
    public function __construct(AuthorizationCheckerInterface $auth)
    {
        $this->auth = $auth;
    }

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
                'username',
                TextType::class,
                [
                    'required' => true,
                    'label' => 'Nom de compte (login) *',
                    'help' => 'Le nom n\'est pas visible au public et est exclusif à la connexion.',
                    'attr' => [
                        'placeholder' => 'Login. Ex: gaetan94',
                        'minLength' => '2',
                        'maxLength' => '64',
                    ],
                ]
            )
            ->add(
                'identity',
                TextType::class,
                [
                    'required' => true,
                    'label' => 'Identité du compte *',
                    'help' => 'L\'identité sera visible par tout le monde. Vous pouvez indiquer le rôle du compte.',
                    'attr' => [
                        'placeholder' => 'Propriétaire du compte. Ex: Admin MDC Michel R.',
                        'minLength' => '2',
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
                    'label' => 'E-mail du compte',
                    'help' => 'L\'email n\'est pas obligatoire,
                    il permet d\'être contacté uniquement pour un éventuel échange hors plateforme
                    au sujet d\'une réduction. Il sera visible pour toute annonce.',
                    'attr' => [
                        'placeholder' => 'Un e-mail. Ex: email@email.fr.',
                        'maxLength' => '64',
                    ],
                ]
            )
            ->add(
                'phoneNumber',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'Téléphone du compte',
                    'help' => 'Le téléphone n\'est pas obligatoire,
                    il permet d\'être contacté uniquement pour un éventuel échange hors plateforme
                    au sujet d\'une réduction. Il sera visible pour toute annonce.',
                    'attr' => [
                        'placeholder' => 'Un numéro. Ex: 06-54-54-54-54 ou +33 6...',
                        'maxLength' => '32',
                    ],
                ]
            )
            ->add(
                'isActive',
                CheckboxType::class,
                [
                    'required' => false,
                    'label'    => 'Profil actif par défaut ?',
                    'help' => 'Vous pouvez désactiver un profil à tout moment'
                ]
            );
        if ($this->auth->isGranted('ROLE_SUPER_ADMIN')) {
            $builder
                ->add(
                    'roles',
                    ChoiceType::class,
                    [
                        'label'    => 'Choississez les droits du compte *',
                        'choices' => [
                            'Utilisateur' => 'ROLE_USER',
                            'Administrateur' => 'ROLE_ADMIN'
                        ],
                        'expanded' => false,
                        'multiple' => false,
                        'empty_data' => 'ROLE_USER',
                        'help' => 'Vous pouvez changer les droits d\'un administrateur ou utilisateur à tout moment.'
                    ]
                )
                ->get('roles')
                ->addModelTransformer(new ArrayToStringTransformer(), true);
        }
        if (empty($options['data']->getPassword())) {
            $builder->add(
                'plainPassword',
                ChangePasswordType::class,
                [
                    'required' => true,
                    'label' => false,
                    'inherit_data' => true,
                    'help' => 'Le mot de passe de l\'utilisateur sera utilisé lors de la connexion.',
                ]
            );
        }
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
        ]);
    }
}
