<?php

declare(strict_types = 1);

namespace App\Tests\Repository\ModelAdapter;

use App\Entity\User;
use App\Service\GlobalClock;
use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityManager;
use App\Repository\UserRepository;
use Symfony\Component\EventDispatcher\EventDispatcher;
use App\Repository\ModelAdapter\EntityRepositoryAdapter;
use App\Repository\ModelAdapter\EntityRepositoryInterface;

/**
 * @group   Unit
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class EntityRepositoryAdapterTest extends TestCase
{
    /** @var EntityRepositoryAdapter */
    private $entityRepositoryAdapter;

    protected function setUp(): void
    {
        $userTestedMock = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $entityManager = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRepository', 'persist', 'flush'])
            ->getMock();

        $entityManager
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($userTestedMock);

        $eventDispatcher = $this->getMockBuilder(EventDispatcher::class)
            ->disableOriginalConstructor()
            ->setMethods(['dispatch'])
            ->getMock();

        $globalClock = $this->getMockBuilder(GlobalClock::class)
            ->disableOriginalConstructor()
            ->setMethods(['getNowInDateTime'])
            ->getMock();

        $this->entityRepositoryAdapter = new EntityRepositoryAdapter($entityManager, $eventDispatcher, $globalClock);
    }

    public function testEntityRepositoryAdapterClassImplementsEntityRepositoryInterface(): void
    {
        $this->assertInstanceOf(EntityRepositoryInterface::class, $this->entityRepositoryAdapter);
    }

    public function testGetRepositoryMethodReturningAValidInstanceOfObjectRepository(): void
    {
        $this->assertInstanceOf(
            UserRepository::class,
            $this->entityRepositoryAdapter->getRepository(User::class)
        );
    }
}
