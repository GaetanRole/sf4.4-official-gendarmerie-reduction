<?php

/**
 * UserIdentity FormType File
 *
 * PHP Version 7.2
 *
 * @category    UserIdentityType
 * @package     App\Form\InheritedForm
 * @version     1.0
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
 *
 * @category    UserIdentityType
 * @package     App\Form\InheritedForm
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
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
                    'empty_data' => 'Aucune identité renseignée.',
                    'label' => 'Votre identité',
                    'help' => 'Le nom n\'est pas obligatoire,
                    il permet simplement de vous identifier auprès des autres utilisateurs 
                    et de suivre un fil de discussion logique.',
                    'attr' => [
                        'placeholder' => 'Indiquez votre identité. Ex: MDC Michel Rolé.',
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
                    'label' => 'Votre e-mail',
                    'help' => 'L\'email n\'est pas obligatoire,
                    il permet d\'être contacté uniquement pour un éventuel échange hors plateforme
                    au sujet d\'une réduction.',
                    'attr' => [
                        'placeholder' => 'Votre e-mail. Ex: email@email.fr.',
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
        ]);
    }
}
