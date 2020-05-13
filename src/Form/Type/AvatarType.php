<?php

declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class AvatarType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['attr']['data-select'] = true;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => $this->getAvatarChoices(),
        ]);
    }

    public function getParent(): ?string
    {
        return ChoiceType::class;
    }

    /**
     * Admin avatar is set by default.
     *
     * @see %kernel.project_dir%/public/assets/images/avatars/uploads
     */
    private function getAvatarChoices(): array
    {
        return [
            'form.avatar.cat' => 'user-avatar-cat.png',
            'form.avatar.elephant' => 'user-avatar-elephant.png',
            'form.avatar.fox' => 'user-avatar-fox.png',
            'form.avatar.monkey' => 'user-avatar-monkey.png',
            'form.avatar.panda' => 'user-avatar-panda.png',
            'form.avatar.penguin' => 'user-avatar-penguin.png',
            'form.avatar.rabbit' => 'user-avatar-rabbit.png',
        ];
    }
}
