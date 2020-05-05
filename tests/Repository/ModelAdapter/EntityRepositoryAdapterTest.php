<?php

declare(strict_types=1);

namespace App\Tests\Repository\ModelAdapter;

use \DateTime;
use \Exception;
use App\Entity\Category;
use App\Repository\Adapter\RepositoryAdapter;
use App\Repository\Adapter\RepositoryAdapterInterface;
use App\Repository\CategoryRepository;
use App\Service\GlobalClock;
use Doctrine\ORM\EntityManagerInterface;
use Innmind\TimeContinuum\Format\ISO8601;
use Innmind\TimeContinuum\TimeContinuum\Earth;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @group   Unit
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class EntityRepositoryAdapterTest extends TestCase
{
    /** @var string To sync with GlobalClock service for assertions */
    private const DATETIME_NOW = 'now';

    /** @var MockObject */
    private $eventDispatcherMock;

    /** @var MockObject */
    private $entityManagerMock;

    /** @var RepositoryAdapter */
    private $entityRepositoryAdapter;

    protected function setUp(): void
    {
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $this->eventDispatcherMock = $this->createMock(EventDispatcherInterface::class);

        $this->entityRepositoryAdapter = new RepositoryAdapter(
            $this->entityManagerMock,
            $this->eventDispatcherMock,
            new GlobalClock(new Earth(), new ISO8601())
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManagerMock->close();
        $this->entityManagerMock = null;
        $this->entityRepositoryAdapter = null;
        $this->eventDispatcherMock = null;
    }

    public function testEntityRepositoryAdapterClassImplementsEntityRepositoryInterface(): void
    {
        $this->assertInstanceOf(RepositoryAdapterInterface::class, $this->entityRepositoryAdapter);
    }

    public function testGetRepositoryMethodReturningAValidObjectRepositoryInstance(): void
    {
        /* Test RepositoryAdapter with a Category::class instance */
        $categoryTestedDummy = $this->createMock(CategoryRepository::class);

        $this->entityManagerMock
            ->expects($this->once())
            ->method('getRepository')
            ->with(Category::class)
            ->willReturn($categoryTestedDummy)
        ;

        $this->assertInstanceOf(
            CategoryRepository::class,
            $this->entityRepositoryAdapter->getRepository(Category::class)
        );
    }

    /**
     * @throws Exception
     */
    public function testSaveMethodSettingWantedAttributesOnAnObjectRepositoryInstanceAndFlushing(): void
    {
        $category = new Category();

        $this->entityManagerMock
            ->expects($this->once())
            ->method('persist')
            ->with($category)
        ;

        $this->entityManagerMock
            ->expects($this->once())
            ->method('flush')
        ;

        $this->eventDispatcherMock
            ->expects($this->once())
            ->method('dispatch')
        ;

        $category = $this->entityRepositoryAdapter->save($category);

        $this->assertInstanceOf(Category::class, $category);
        // Expected value on DateTime now, not GlobalClock (to check if GlobalClock is async)
        $this->assertEqualsWithDelta(new DateTime(self::DATETIME_NOW), $category->getCreatedAt(), 5.0);
        $this->assertTrue(Uuid::isValid($category->getUuid()->toString()));
    }

    /**
     * @throws Exception
     */
    public function testUpdateMethodUpdatingOneObjectAttributeAndFlushing(): void
    {
        $category = new Category();

        $this->entityManagerMock
            ->expects($this->once())
            ->method('flush')
        ;

        $this->eventDispatcherMock
            ->expects($this->once())
            ->method('dispatch')
        ;

        $this->assertNull($category->getUpdatedAt());

        $category = $this->entityRepositoryAdapter->update($category);

        $this->assertInstanceOf(Category::class, $category);
        $this->assertEqualsWithDelta(new DateTime(self::DATETIME_NOW), $category->getUpdatedAt(), 5.0);
    }

    public function testDeleteMethodAnObjectInstanceAndFlushing(): void
    {
        $category = new Category();

        $this->entityManagerMock
            ->expects($this->once())
            ->method('remove')
        ;

        $this->entityManagerMock
            ->expects($this->once())
            ->method('flush')
        ;

        $this->eventDispatcherMock
            ->expects($this->once())
            ->method('dispatch')
        ;

        $this->entityRepositoryAdapter->delete($category);
    }
}
