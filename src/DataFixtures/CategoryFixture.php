<?php

declare(strict_types=1);

namespace App\DataFixtures;

use \Exception;
use App\Entity\Category;
use App\Service\GlobalClock;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Ramsey\Uuid\Uuid;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @see     https://symfony.com/doc/master/bundles/DoctrineFixturesBundle/index.html
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class CategoryFixture extends Fixture implements FixtureGroupInterface
{
    /** @var string public CONST for reference used in ReductionFixture, concat to an int [0-4]. */
    public const CATEGORY_REFERENCE = 'category-';

    /**
     * Global project's clock.
     *
     * @var GlobalClock
     */
    private $clock;

    /** @var ContainerInterface */
    private $container;

    /**
     * @see    https://github.com/Innmind/TimeContinuum Global clock
     */
    public function __construct(GlobalClock $clock, ContainerInterface $container)
    {
        $this->clock = $clock;
        $this->container = $container;
    }

    /**
     * Load three types of Categories to DB.
     *
     * @see     3 $categories[5] to use its references in ReductionFixture
     * @see    https://github.com/fzaninotto/Faker
     *
     * @throws Exception Datetime Exception
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create($this->container->getParameter('faker_locale'));

        foreach ($this->getCategories() as $index => $name) {
            $category = new Category();

            $category->setUuid(Uuid::uuid4());
            $category->setCreatedAt($this->clock->getNowInDateTime());
            $category->setUpdatedAt(null);

            $category->setName(ucfirst($name))
                ->setDescription($faker->realText(100))
            ;

            $manager->persist($category);
            $this->addReference(self::CATEGORY_REFERENCE.$index, $category);
        }

        $manager->flush();
    }

    /**
     * Get an array of Category names.
     */
    private function getCategories(): array
    {
        return [
            'vacances',
            'au quotidien',
            'réduction immédiate',
            'soldes',
            'meilleure réduction',
        ];
    }

    /**
     * Get User / Brand / Category fixtures.
     */
    public static function getGroups(): array
    {
        return ['independent'];
    }
}
