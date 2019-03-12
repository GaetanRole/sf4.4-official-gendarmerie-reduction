<?php

/**
 * ReductionFixture file
 *
 * PHP Version 7.2
 *
 * @category    Reduction
 * @package     App\DataFixtures
 * @version     1.0
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */

namespace App\DataFixtures;

use App\Entity\Reduction;
use App\Utils\Slugger;
use Faker;
use App\Service\GlobalClock;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * ReductionFixture class
 *
 * @see         https://symfony.com/doc/master/bundles/DoctrineFixturesBundle/index.html
 * @category    Reduction
 * @package     App\DataFixtures
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class ReductionFixture extends Fixture implements DependentFixtureInterface
{
    /**
     * @var int public CONST for Reductions number in DB
     */
    public const REDUCTION_NB_TUPLE = 20;

    /**
     * @var string public CONST for reference, concat to an int [0-REDUCTION_NB_TUPLE]
     */
    public const REDUCTION_REFERENCE = 'reduction-';

    /**
     * Global project's clock
     *
     * @var GlobalClock
     */
    private $clock;

    /**
     * ReductionFixture constructor.
     *
     * @link https://github.com/Innmind/TimeContinuum Global clock
     * @param GlobalClock $clock Global project's clock
     */
    public function __construct(GlobalClock $clock)
    {
        $this->clock = $clock;
    }

    /**
     * Load REDUCTION_NB_TUPLE reductions to DB
     *
     * @link https://github.com/fzaninotto/Faker
     * @see 3 Loop iterator depends on const REDUCTION_NB_TUPLE
     * @param ObjectManager $manager Doctrine Manager
     *
     * @return void
     * @throws \Exception Datetime Exception
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        foreach ($this->getReductionData() as [$index, $author, $brand, $categories]) {
            $reduction = new Reduction();
            $reduction->setUser($author);
            $reduction->setClientIp($faker->ipv4);
            $reduction->setName($faker->userName);
            $reduction->setEmail($faker->email);
            $reduction->setBrand($brand);
            $reduction->addCategory(...$categories);
            $reductionTitle = $index . ' ' . $faker->text(16);
            $reduction->setTitle($reductionTitle);
            $reduction->setSlug(Slugger::slugify($reductionTitle));
            $reduction->setDescription($faker->realText(300));
            $reduction->setRegion($this->getRandomRegion());
            $reduction->setDepartment($this->getRandomDepartment());
            $reduction->setCity($faker->city);
            $reduction->setCreationDate($this->clock->getNowInDateTime());
            $reduction->setIsBigDeal((bool)random_int(0, 1));
            $reduction->setIsActive((bool)random_int(0, 1));

            $manager->persist($reduction);
            $this->addReference(self::REDUCTION_REFERENCE.$index, $reduction);
        }

        $manager->flush();
    }

    /**
     * Get a random Region
     *
     * @return string
     */
    private function getRandomRegion(): string
    {
        $regions = [
            'Hauts-de-France',
            'Île-de-France',
            'La Réunion',
            'Bourgogne-Franche-Comté',
            'Provence-Alpes-Côte d\'Azur',
        ];

        return $regions[array_rand($regions)];
    }

    /**
     * Get a random Department
     *
     * @return string
     */
    private function getRandomDepartment(): string
    {
        $departments = [
            'Seine-Maritime',
            'Calvados',
            'Eure',
            'Nord',
            'Pas-de-Calais',
        ];

        return $departments[array_rand($departments)];
    }

    /**
     * Get an array of references useful for Reduction instances
     *
     * @see 7 See UserFixture::USER_NB_TUPLE - 1 for index 0
     *
     * @return array
     * @throws \Exception Random Exception
     */
    private function getReductionData(): array
    {
        $reductions = [];
        for ($index = 0; $index < self::REDUCTION_NB_TUPLE; $index++) {
            // $reduction = [$index, $author, $brand, $categories, $reference];
            $reductions[] = [
                $index,
                $this->getReference(
                    UserFixture::USER_REFERENCE.random_int(0, UserFixture::USER_NB_TUPLE - 1)
                ),
                $this->getReference($this->getRandomBrand()),
                $this->getRandomCategories(),
            ];
        }

        return $reductions;
    }

    /**
     * Get a random Brand from BrandFixture
     *
     * @return string
     * @throws \Exception Random Exception
     */
    private function getRandomBrand(): string
    {
        $brandReferences = [
            BrandFixture::FOOD_BRAND_REFERENCE,
            BrandFixture::CAR_BRAND_REFERENCE,
            BrandFixture::PARK_BRAND_REFERENCE,
        ];

        return $brandReferences[array_rand($brandReferences)].random_int(0, 4);
    }

    /**
     * Get an array of random Category references
     *
     * @return array
     * @throws \Exception Random Exception
     */
    private function getRandomCategories(): array
    {
        $randomCategoryReferences = [];
        // * @Assert\Count(max="3")
        $maxIndex = random_int(1, 4);

        for ($index = 0; $index < $maxIndex; $index++) {
            $randomCategoryReferences[]
                = $this->getReference(CategoryFixture::CATEGORY_REFERENCE.random_int(0, 4));
        }

        return $randomCategoryReferences;
    }

    /**
     * Get dependencies from entity relations
     *
     * @return array
     */
    public function getDependencies(): array
    {
        return [
            UserFixture::class,
            BrandFixture::class,
            CategoryFixture::class,
        ];
    }
}
