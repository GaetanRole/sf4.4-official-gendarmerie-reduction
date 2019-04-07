<?php

/**
 * CategoryFixture file
 *
 * @category    Category
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */

namespace App\DataFixtures;

use App\Entity\Category;
use Faker;
use App\Service\GlobalClock;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @see         https://symfony.com/doc/master/bundles/DoctrineFixturesBundle/index.html
 */
final class CategoryFixture extends Fixture implements FixtureGroupInterface
{
    /**
     * @var string public CONST for reference used in ReductionFixture, concat to an int [0-4]
     */
    public const CATEGORY_REFERENCE = 'category-';

    /**
     * Global project's clock
     *
     * @var GlobalClock
     */
    private $clock;

    /**
     * Injecting Container Interface
     *
     * @var ContainerInterface
     */
    private $container;

    /**
     * CategoryFixture constructor.
     *
     * @link https://github.com/Innmind/TimeContinuum Global clock
     * @param GlobalClock $clock Global project's clock
     * @param ContainerInterface $container Container Interface
     */
    public function __construct(GlobalClock $clock, ContainerInterface $container)
    {
        $this->clock = $clock;
        $this->container = $container;
    }

    /**
     * Load three types of Categories to DB
     *
     * @link https://github.com/fzaninotto/Faker
     * @see 3 $categories[5] to use its references in ReductionFixture
     * @param ObjectManager $manager Doctrine Manager
     *
     * @return void
     * @throws \Exception Datetime Exception
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create($this->container->getParameter('faker_locale'));

        foreach ($this->getCategories() as $index => $name) {
            $category = new Category();
            $category->setName(ucfirst($name));
            $category->setDescription($faker->text);
            $category->setCreationDate($this->clock->getNowInDateTime());

            $manager->persist($category);
            $this->addReference(self::CATEGORY_REFERENCE.$index, $category);
        }

        $manager->flush();
    }

    /**
     * Get an array of Category names
     *
     * @return array
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
     * Get User / Brand / Category fixtures
     *
     * @return array
     */
    public static function getGroups(): array
    {
        return ['independent'];
    }
}
