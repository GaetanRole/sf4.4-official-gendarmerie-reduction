<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Service\PromoGendMailer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

/**
 * Override the "from" field for each mail.
 *
 * @todo Write unit tests for this subscriber.
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class MailerSetFromFieldSubscriber implements EventSubscriberInterface
{
    /** @var string */
    private $sender;

    public function __construct(string $sender)
    {
        $this->sender = $sender;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MessageEvent::class => 'onMessage',
        ];
    }

    public function onMessage(MessageEvent $event): void
    {
        $email = $event->getMessage();

        if (!$email instanceof Email) {
            return;
        }

        $email->from(new Address($this->sender, PromoGendMailer::MAILER_NAME));
    }
}
