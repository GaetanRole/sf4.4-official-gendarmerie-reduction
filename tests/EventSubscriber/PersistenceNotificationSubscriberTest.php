<?php

declare(strict_types = 1);

namespace App\Tests\EventSubscriber;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\Translator;
use App\Event\SuccessPersistenceNotificationEvent;
use App\EventSubscriber\PersistenceNotificationSubscriber;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;

/**
 * @group   Unit
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class PersistenceNotificationSubscriberTest extends TestCase
{
    /** @var PersistenceNotificationSubscriber */
    private $listener;

    /** @var SuccessPersistenceNotificationEvent */
    private $event;

    protected function setUp(): void
    {
        $this->listener = new PersistenceNotificationSubscriber(new FlashBag(), new Translator(null));
        $this->event = $this->createMock(SuccessPersistenceNotificationEvent::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->listener = null;
        $this->event = null;
    }

    public function testGetSubscribedEventsReturningAGoodEventClassAndName(): void
    {
        $this->assertSame(
            [SuccessPersistenceNotificationEvent::class => 'onSuccessPersistenceNotification'],
            $this->listener::getSubscribedEvents()
        );
    }

    public function testOnSuccessPersistenceNotificationEventIsCalledWithAGoodEvent(): void
    {
        $this->event->expects($this->once())->method('getTranslationKey');
        $this->listener->onSuccessPersistenceNotification($this->event);
    }
}
