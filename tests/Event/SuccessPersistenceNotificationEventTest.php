<?php

declare(strict_types=1);

namespace App\Tests\Event;

use \ReflectionClass;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\EventDispatcher\Event;
use App\Event\SuccessPersistenceNotificationEvent;

/**
 * @group   Unit
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class SuccessPersistenceNotificationEventTest extends TestCase
{
    public function testSuccessPersistenceNotificationEventClassImplementsEventClass(): void
    {
        $this->assertInstanceOf(
            Event::class,
            new SuccessPersistenceNotificationEvent('')
        );
    }

    public function testSuccessPersistenceNotificationEventHasTypeAndDomainConstants(): void
    {
        $reflectedEvent = new ReflectionClass(SuccessPersistenceNotificationEvent::class);
        $this->assertArrayHasKey('TYPE', $reflectedEvent->getConstants());
        $this->assertArrayHasKey('DOMAIN', $reflectedEvent->getConstants());
        $this->assertEquals('info', $reflectedEvent->getConstant('TYPE'));
        $this->assertEquals('flashes', $reflectedEvent->getConstant('DOMAIN'));
    }

    public function testGetTranslationKeyMethodReturningAGoodTranslationKey(): void
    {
        $event = new SuccessPersistenceNotificationEvent('test.key');
        $this->assertSame('test.key', $event->getTranslationKey());
    }
}
