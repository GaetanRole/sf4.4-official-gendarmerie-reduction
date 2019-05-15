<?php

namespace App\DataFixtures;

use App\Entity\Reduction;
use App\Utils\Slugger;
use Faker;
use Exception;
use App\Service\GlobalClock;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @see     https://symfony.com/doc/master/bundles/DoctrineFixturesBundle/index.html
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class ReductionFixture extends Fixture implements DependentFixtureInterface
{
    /** @var int public CONST for Reductions number in DB */
    public const REDUCTION_NB_TUPLE = 20;

    /** @var string public CONST for reference, concat to an int [0-REDUCTION_NB_TUPLE] */
    public const REDUCTION_REFERENCE = 'reduction-';

    /**
     * Global project's clock
     * @var GlobalClock
     */
    private $clock;

    /**
     * Injecting Container Interface
     * @var ContainerInterface
     */
    private $container;

    /**
     * @link https://github.com/Innmind/TimeContinuum Global clock
     */
    public function __construct(GlobalClock $clock, ContainerInterface $container)
    {
        $this->clock = $clock;
        $this->container = $container;
    }

    /**
     * Load REDUCTION_NB_TUPLE reductions to DB.
     * @link https://github.com/fzaninotto/Faker
     * @see 3 Loop iterator depends on const REDUCTION_NB_TUPLE
     * @throws Exception Datetime Exception
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create($this->container->getParameter('faker_locale'));

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
            $reduction->setMunicipality($faker->city);
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
     */
    private function getRandomRegion(): string
    {
        $regions = [
            '01',
            '93',
            '52',
            '76',
            '32',
        ];

        return $regions[array_rand($regions)];
    }

    /**
     * Get a random Department
     */
    private function getRandomDepartment(): string
    {
        $departments = [
            '59',
            '976',
            '64',
            '93',
            '06',
        ];

        return $departments[array_rand($departments)];
    }

    /**
     * Get an array of references useful for Reduction instances
     * @see 7 See UserFixture::USER_NB_TUPLE - 1 for index 0
     * @throws Exception Random Exception
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
     * @throws Exception Random Exception
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
     * @throws Exception Random Exception
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
