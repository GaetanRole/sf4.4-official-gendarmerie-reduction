<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'form.contact.name.label',
                'attr' => ['placeholder' => 'form.contact.name.placeholder', 'autofocus' => true],
                'constraints' => [
                    new NotBlank(['message' => 'form.contact.name.not_blank']),
                    new Length(['min' => 2, 'max' => 32]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'form.contact.email.label',
                'attr' => ['placeholder' => 'form.contact.email.placeholder'],
                'constraints' => [
                    new NotBlank(['message' => 'form.contact.email.not_blank']),
                    new Email(['message' => 'form.contact.email.email']),
                    new Length(['min' => 6, 'max' => 64]),
                ],
            ])
            ->add('subject', TextType::class, [
                'label' => 'form.contact.subject.label',
                'attr' => ['placeholder' => 'form.contact.subject.placeholder'],
                'constraints' => [
                    new NotBlank(['message' => 'form.contact.subject.not_blank']),
                    new Length(['min' => 6, 'max' => 128]),
                ],
            ])
            ->add('message', TextareaType::class, [
                'label' => 'form.contact.message.label',
                'attr' => ['placeholder' => 'form.contact.message.placeholder'],
                'constraints' => [
                    new NotBlank(['message' => 'form.contact.message.not_blank']),
                    new Length(['min' => 12, 'max' => 254]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['translation_domain' => 'forms', 'attr' => ['id' => 'contact-form']]);
    }
}
