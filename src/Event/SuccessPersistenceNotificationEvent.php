<?php

declare(strict_types=1);

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
class SuccessPersistenceNotificationEvent extends Event
{
    public const
        /** @var string FlashBag type for FlashBagInterface::add(). */
        TYPE   = 'success',
        /** @var string FlashBag domain for catalog. */
        DOMAIN = 'flashes';

    /** @var string */
    private $translationKey;

    public function __construct(string $translationKey)
    {
        $this->translationKey = $translationKey;
    }

    public function getTranslationKey(): string
    {
        return $this->translationKey;
    }
}
