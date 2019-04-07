<?php

/**
 * BrandFixture file
 *
 * PHP Version 7.2
 *
 * @category    Brand
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */

namespace App\DataFixtures;

use App\Entity\Brand;
use Faker;
use App\Service\GlobalClock;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @see         https://symfony.com/doc/master/bundles/DoctrineFixturesBundle/index.html
 */
final class BrandFixture extends Fixture implements FixtureGroupInterface
{
    public const
        /**
         * @var string CONST for food reference, concat to an int [0-4]
         */
        FOOD_BRAND_REFERENCE = 'food-brand-',
        /**
         * @var string CONST for car reference, concat to an int [0-4]
         */
        CAR_BRAND_REFERENCE = 'car-brand-',
        /**
         * @var string CONST for park reference, concat to an int [0-4]
         */
        PARK_BRAND_REFERENCE = 'park-brand-';

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
     * BrandFixture constructor.
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
     * Load three types of Brands to DB
     *
     * @link https://github.com/fzaninotto/Faker
     * @param ObjectManager $manager Doctrine Manager
     *
     * @return void
     * @throws \Exception Datetime Exception
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create($this->container->getParameter('faker_locale'));

        $this->loadFoodBrands($manager, $faker);
        $this->loadCarBrands($manager, $faker);
        $this->loadParkBrands($manager, $faker);
    }

    /**
     * Load five types of food Brands
     *
     * @see See BrandFixture::FOOD_BRAND_REFERENCE value
     * @param ObjectManager $manager Doctrine Manager
     * @param Faker\Generator $faker To get random text
     *
     * @return void
     * @throws \Exception Datetime Exception
     */
    private function loadFoodBrands(
        ObjectManager $manager,
        Faker\Generator $faker
    ): void {
        $foodBrandData
            = ['KFC', 'McDonald\'s', 'Au Bureau', 'Burger King', 'Léon'];

        foreach ($foodBrandData as $index => $name) {
            $brand = new Brand();
            $brand->setName($name);
            $brand->setDescription($faker->text);
            $brand->setCreationDate($this->clock->getNowInDateTime());

            $manager->persist($brand);
            $this->addReference(self::FOOD_BRAND_REFERENCE.$index, $brand);
        }

        $manager->flush();
    }

    /**
     * Load five types of car Brands
     *
     * @see See BrandFixture::CAR_BRAND_REFERENCE value
     * @param ObjectManager $manager Doctrine Manager
     * @param Faker\Generator $faker To get random text
     *
     * @return void
     * @throws \Exception Datetime Exception
     */
    private function loadCarBrands(
        ObjectManager $manager,
        Faker\Generator $faker
    ): void {
        $carBrandData
            = ['Midas', 'Citroën', 'Aramis Auto', 'Peugeot', 'Speedy'];

        foreach ($carBrandData as $index => $name) {
            $brand = new Brand();
            $brand->setName($name);
            $brand->setDescription($faker->text);
            $brand->setCreationDate($this->clock->getNowInDateTime());

            $manager->persist($brand);
            $this->addReference(self::CAR_BRAND_REFERENCE.$index, $brand);
        }

        $manager->flush();
    }

    /**
     * Load five types of park Brands
     *
     * @see See BrandFixture::PARK_BRAND_REFERENCE value
     * @param ObjectManager $manager Doctrine Manager
     * @param Faker\Generator $faker To get random text
     *
     * @return void
     * @throws \Exception Datetime Exception
     */
    private function loadParkBrands(
        ObjectManager $manager,
        Faker\Generator $faker
    ): void {
        $parkBrandData
            = ['Astérix', 'Walibi', 'DisneyLand', 'Aqualud', 'Futuroscope'];

        foreach ($parkBrandData as $index => $name) {
            $brand = new Brand();
            $brand->setName($name);
            $brand->setDescription($faker->text);
            $brand->setCreationDate($this->clock->getNowInDateTime());

            $manager->persist($brand);
            $this->addReference(self::PARK_BRAND_REFERENCE.$index, $brand);
        }

        $manager->flush();
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
