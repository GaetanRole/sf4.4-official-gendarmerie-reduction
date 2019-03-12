<?php

/**
 * Opinion FormType File
 *
 * PHP Version 7.2
 *
 * @category    Opinion
 * @package     App\Form
 * @version     1.0
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */

namespace App\Form;

use App\Entity\Opinion;
use App\Form\InheritedForm\UserIdentityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Opinion FormType Class
 *
 * @category    Opinion
 * @package     App\Form
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
class OpinionType extends AbstractType
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
                'userIdentity',
                UserIdentityType::class,
                [
                    'data_class' => Opinion::class
                ]
            )
            ->add(
                'comment',
                TextareaType::class,
                [
                    'required' => true,
                    'label' => 'Votre commentaire *',
                    'help' => 'Les commentaires non conformes à notre code de conduite seront modérés.',
                    'attr' => [
                        'placeholder' => 'Ex: Merci pour cette réduction de 15% !',
                        'minLength' => '5',
                        'maxLength' => '10000',
                        'rows' => 30,
                    ],
                ]
            );
    }

    /**
     * Set Opinion class
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Opinion::class,
        ]);
    }
}
