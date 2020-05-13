<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class ArticleType extends AbstractType
{
    /**
     * @todo Add CollectionType for resources field.
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'form.article.title.label',
                'help' => 'form.article.title.help',
                'attr' => [
                    'placeholder' => 'form.article.title.placeholder',
                    'minLength' => '5',
                    'maxLength' => '64',
                ],
            ])
            ->add('summary', TextType::class, [
                'label' => 'form.article.summary.label',
                'help' => 'form.article.summary.help',
                'attr' => [
                    'placeholder' => 'form.article.summary.placeholder',
                    'minLength' => '5',
                    'maxLength' => '254',
                ],
            ])
            ->add('content', TextareaType::class, [
                'label' => 'form.article.content.label',
                'help' => 'form.article.content.help',
                'attr' => [
                    'placeholder' => 'form.article.content.placeholder',
                    'minLength' => '10',
                    'maxLength' => '1024',
                    'rows' => 10,
                ],
            ])
            ->add('priority', ChoiceType::class, [
                'expanded' => false,
                'multiple' => false,
                'choices' => [
                    'form.article.priority.choices.low' => Article::PRIORITY['LOW'],
                    'form.article.priority.choices.medium' => Article::PRIORITY['MEDIUM'],
                    'form.article.priority.choices.high' => Article::PRIORITY['HIGH'],
                    'form.article.priority.choices.urgent' => Article::PRIORITY['URGENT'],
                ],
                'attr' => [
                    'data-select' => true,
                ],
                'label' => 'form.article.priority.label',
                'help' => 'form.article.priority.help',
            ])
            ->add('isActive', CheckboxType::class, [
                'required' => false,
                'label' => 'form.article.is_active.label',
                'help' => 'form.article.is_active.help',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
            'translation_domain' => 'forms',
        ]);
    }
}
