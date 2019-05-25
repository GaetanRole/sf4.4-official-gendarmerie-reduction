<?php

namespace App\DataFixtures;

use Faker;
use Exception;
use App\Entity\Brand;
use App\Service\GlobalClock;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @see     https://symfony.com/doc/master/bundles/DoctrineFixturesBundle/index.html
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class BrandFixture extends Fixture implements FixtureGroupInterface
{
    public const
        /** @var string CONST for food reference, concat to an int [0-4]. */
        FOOD_BRAND_REFERENCE = 'food-brand-',
        /** @var string CONST for car reference, concat to an int [0-4]. */
        CAR_BRAND_REFERENCE = 'car-brand-',
        /** @var string CONST for park reference, concat to an int [0-4]. */
        PARK_BRAND_REFERENCE = 'park-brand-';

    public const
        /** @var array CONST for five food brands fixture. */
        FOOD_BRAND_DATA = ['KFC', 'McDonald\'s', 'Au Bureau', 'Burger King', 'Léon'],
        /** @var array CONST for five car brands fixture. */
        CAR_BRAND_DATA = ['Midas', 'Citroën', 'Aramis Auto', 'Peugeot', 'Speedy'],
        /** @var array CONST for five park brands fixture. */
        PARK_BRAND_DATA = ['Astérix', 'Walibi', 'DisneyLand', 'Aqualud', 'Futuroscope'];

    /**
     * Global project's clock.
     *
     * @var GlobalClock
     */
    private $clock;

    /** @var ContainerInterface */
    private $container;

    /** @var ObjectManager */
    private $manager;

    /**
     * @link    https://github.com/Innmind/TimeContinuum Global clock
     */
    public function __construct(GlobalClock $clock, ContainerInterface $container)
    {
        $this->clock = $clock;
        $this->container = $container;
    }

    /**
     * Load three types of Brands to DB.
     *
     * @link    https://github.com/fzaninotto/Faker
     * @throws  Exception Datetime Exception
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create($this->container->getParameter('faker_locale'));
        $this->manager = $manager;

        $this->loadBrands($faker, self::FOOD_BRAND_REFERENCE, self::FOOD_BRAND_DATA);
        $this->loadBrands($faker, self::CAR_BRAND_REFERENCE, self::CAR_BRAND_DATA);
        $this->loadBrands($faker, self::PARK_BRAND_REFERENCE, self::PARK_BRAND_DATA);
    }

    /**
     * Load five types of food, car and park Brands.
     *
     * @see     See BrandFixture::TYPE_BRAND_REFERENCE and BrandFixture::TYPE_BRAND_DATA values
     * @throws  Exception Datetime Exception
     */
    private function loadBrands(Faker\Generator $faker, string $brandReference, array $brandData): void
    {
        foreach ($brandData as $index => $name) {
            $brand = new Brand();
            $brand->setName($name);
            $brand->setDescription($faker->realText(100));
            $brand->setCreationDate($this->clock->getNowInDateTime());

            $this->manager->persist($brand);
            $this->addReference($brandReference.$index, $brand);
        }

        $this->manager->flush();
    }

    /**
     * Get User / Brand / Category fixtures.
     */
    public static function getGroups(): array
    {
        return ['independent'];
    }
}
