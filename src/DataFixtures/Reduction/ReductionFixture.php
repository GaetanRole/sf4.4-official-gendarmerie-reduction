<?php

declare(strict_types=1);

namespace App\DataFixtures\Reduction;

use \Exception;
use App\DataFixtures\BrandFixture;
use App\DataFixtures\CategoryFixture;
use App\DataFixtures\UserFixture;
use App\Entity\Image;
use App\Entity\Reduction;
use App\Service\GlobalClock;
use App\Utils\FilesystemStaticUtilities;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use EasySlugger\SluggerInterface;
use Faker;
use Ramsey\Uuid\Uuid;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @see     https://symfony.com/doc/master/bundles/DoctrineFixturesBundle/index.html
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class ReductionFixture extends Fixture implements DependentFixtureInterface
{
    /** @var int public CONST for Reductions number in DB. */
    public const REDUCTION_NB_TUPLE = 20;

    /** @var string public CONST for reference, concat to an int [0-REDUCTION_NB_TUPLE]. */
    public const REDUCTION_REFERENCE = 'reduction-';

    /**
     * Global project's clock.
     *
     * @var GlobalClock
     */
    private $clock;

    /** @var ContainerInterface */
    private $container;

    /** @var SluggerInterface */
    private $slugger;

    /** @var string */
    private $imageUploadDirectory;

    /**
     * @see    https://github.com/Innmind/TimeContinuum Global clock
     */
    public function __construct(
        GlobalClock $clock,
        ContainerInterface $container,
        SluggerInterface $slugger,
        string $imageUploadDirectory
    ) {
        $this->clock = $clock;
        $this->container = $container;
        $this->slugger = $slugger;
        $this->imageUploadDirectory = $imageUploadDirectory;
    }

    /**
     * Load REDUCTION_NB_TUPLE reductions to DB.
     *
     * @see     3 Loop iterator depends on const REDUCTION_NB_TUPLE
     * @see    https://github.com/fzaninotto/Faker
     *
     * @throws Exception Datetime Exception
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create($this->container->getParameter('faker_locale'));

        foreach ($this->getReductionData() as [$index, $author, $brand, $categories, $image]) {
            $reduction = new Reduction();

            $reduction->setUuid(Uuid::uuid4());
            $reduction->setClientIp($faker->ipv4);
            $reduction->setName($faker->userName);
            $reduction->setEmail($faker->email);
            $reduction->setCreatedAt($this->clock->getNowInDateTime());
            $reduction->setUpdatedAt(null);

            $reductionTitle = $index.' '.$faker->text(16);
            $reduction->setUser($author)
                ->setBrand($brand)
                ->addCategory(...$categories)
                ->setTitle($reductionTitle)
                ->setSlug($this->slugger::uniqueSlugify($reductionTitle))
                ->setDescription($faker->realText(300))
                ->setImage($image)
                ->setRegion($this->getRandomRegion())
                ->setDepartment($this->getRandomDepartment())
                ->setMunicipality($faker->city)
                ->setIsBigDeal((bool) random_int(0, 1))
                ->setIsActive((bool) random_int(0, 1))
            ;

            $manager->persist($reduction);
            $this->addReference(self::REDUCTION_REFERENCE.$index, $reduction);
        }

        $manager->flush();
    }

    /**
     * Get a random Region.
     */
    private function getRandomRegion(): string
    {
        $regions = ['01', '93', '52', '76', 'International'];

        return $regions[array_rand($regions)];
    }

    /**
     * Get a random Department.
     */
    private function getRandomDepartment(): string
    {
        $departments = ['59', '976', '64', '93', '06'];

        return $departments[array_rand($departments)];
    }

    /**
     * Get an array of references useful for Reduction instances.
     *
     * @see     7 See UserFixture::USER_NB_TUPLE - 1 for index 0
     *
     * @throws Exception Random Exception
     */
    private function getReductionData(): array
    {
        $reductions = [];
        for ($index = 0; $index < self::REDUCTION_NB_TUPLE; ++$index) {
            // $reduction = [$index, $author, $brand, $categories, image];
            $reductions[] = [
                $index,
                $this->getReference(UserFixture::USER_REFERENCE.random_int(0, UserFixture::USER_NB_TUPLE - 1)),
                $this->getReference($this->getRandomBrand()),
                $this->getRandomCategories(),
                $this->getAStaticImageFixture(),
            ];
        }

        return $reductions;
    }

    /**
     * Get a random Brand from BrandFixture.
     *
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
     * Get an array of random Category references.
     *
     * @throws Exception Random Exception
     */
    private function getRandomCategories(): array
    {
        $randomCategoryReferences = [];
        // * @Assert\Count(max="3")
        $maxIndex = random_int(1, 3);

        for ($index = 0; $index < $maxIndex; ++$index) {
            $randomCategoryReferences[]
                = $this->getReference(CategoryFixture::CATEGORY_REFERENCE.random_int(0, 4));
        }

        return $randomCategoryReferences;
    }

    /**
     * Create a fake Image object based on a static image fixture
     *
     * @throws Exception Datetime Exception
     */
    private function getAStaticImageFixture(): Image
    {
        $image = new Image();
        $image->setUuid(Uuid::uuid4());
        $image->setCreatedAt($this->clock->getNowInDateTime());
        $image->setUpdatedAt(null);
        $image->setExtension('jpeg');

        FilesystemStaticUtilities::forceFileCopying(
            __DIR__.'/base-image-fixture.jpeg',
            $this->imageUploadDirectory,
            '/'.$image->getUuid()->toString().'.'.$image->getExtension()
        );

        $image->setFile(
            new File($this->imageUploadDirectory.'/'.$image->getUuid()->toString().'.'.$image->getExtension())
        );

        return $image;
    }

    /**
     * Get dependencies from entity relations.
     */
    public function getDependencies(): array
    {
        return [UserFixture::class, BrandFixture::class, CategoryFixture::class];
    }
}
