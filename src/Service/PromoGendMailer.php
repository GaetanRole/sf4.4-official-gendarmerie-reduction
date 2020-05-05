<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

/**
 * @todo Write unit tests for this service: https://symfonycasts.com/screencast/mailer/unit-test
 * @todo Set async emails: https://symfonycasts.com/screencast/mailer/async-emails
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
class PromoGendMailer
{
    /** @var string */
    public const MAILER_NAME = 'PromoGend';

    /** @var MailerInterface */
    private $mailer;

    /** @var string */
    private $recipient;

    public function __construct(MailerInterface $mailer, string $recipient)
    {
        $this->mailer = $mailer;
        $this->recipient = $recipient;
    }

    /**
     * ->from() is set in MailerSetFromFieldSubscriber.
     *
     * @throws TransportExceptionInterface
     */
    public function send(
        string $subject,
        string $template,
        array $context,
        int $priority = Email::PRIORITY_NORMAL
    ): void {
        $email = (new TemplatedEmail())
            ->to($this->recipient)
            ->subject($subject)
            ->priority($priority)
            ->htmlTemplate($template)
            ->context([
                'sender_name' => $context['name'],
                'sender_email' => $context['email'],
                'subject' => $context['subject'],
                'message' => $context['message'],
            ])
        ;

        $this->mailer->send($email);
    }
}
