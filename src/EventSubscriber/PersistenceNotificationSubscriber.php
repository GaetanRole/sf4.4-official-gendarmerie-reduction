<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Event\SuccessPersistenceNotificationEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Notifies all persistence from RepositoryAdapter::save(), ::update() and ::delete() methods.
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class PersistenceNotificationSubscriber implements EventSubscriberInterface
{
    /** @var FlashBagInterface */
    private $flashBag;

    /** @var TranslatorInterface */
    private $translator;

    public function __construct(FlashBagInterface $flashBag, TranslatorInterface $translator)
    {
        $this->flashBag = $flashBag;
        $this->translator = $translator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SuccessPersistenceNotificationEvent::class => 'onSuccessPersistenceNotification',
        ];
    }

    public function onSuccessPersistenceNotification(SuccessPersistenceNotificationEvent $event): void
    {
        $this->flashBag->add(
            SuccessPersistenceNotificationEvent::TYPE,
            $this->translator->trans($event->getTranslationKey(), [], SuccessPersistenceNotificationEvent::DOMAIN),
        );
    }
}
