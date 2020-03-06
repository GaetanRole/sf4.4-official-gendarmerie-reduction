<?php

declare(strict_types = 1);

namespace App\DataFixtures;

use Faker;
use \Exception;
use Ramsey\Uuid\Uuid;
use App\Entity\Opinion;
use App\Services\GlobalClock;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @see     https://symfony.com/doc/master/bundles/DoctrineFixturesBundle/index.html
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class OpinionFixture extends Fixture implements DependentFixtureInterface
{
    /** @var int public CONST for Opinions number in DB. */
    public const OPINION_NB_TUPLE = 40;

    /**
     * Global project's clock.
     *
     * @var GlobalClock
     */
    private $clock;

    /** @var ContainerInterface */
    private $container;

    /**
     * @link    https://github.com/Innmind/TimeContinuum Global clock
     */
    public function __construct(GlobalClock $clock, ContainerInterface $container)
    {
        $this->clock = $clock;
        $this->container = $container;
    }

    /**
     * Load OPINION_NB_TUPLE opinions to DB.
     *
     * @see     3 Loop iterator depends on const OPINION_NB_TUPLE
     * @link    https://github.com/fzaninotto/Faker
     * @throws  Exception Datetime Exception
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create($this->container->getParameter('faker_locale'));

        foreach ($this->getOpinionReferenceData() as [$author, $reduction]) {
            $opinion = new Opinion();

            $opinion->setUuid(Uuid::uuid4());
            $opinion->setName($faker->userName);
            $opinion->setEmail($faker->email);
            $opinion->setClientIp($faker->ipv4);
            $opinion->setCreatedAt($this->clock->getNowInDateTime());
            $opinion->setUpdatedAt(null);

            $opinion->setUser($author)
                ->setReduction($reduction)
                ->setComment($faker->realText(100));

            $manager->persist($opinion);
        }

        $manager->flush();
    }

    /**
     * Get an array of references useful for Opinion instances.
     *
     * @see     9 See UserFixture::USER_NB_TUPLE - 1 for index 0
     * @see     15 See ReductionFixture::REDUCTION_NB_TUPLE - 1 for index 0
     * @throws  Exception Random Exception
     */
    private function getOpinionReferenceData(): array
    {
        $opinionData = [];
        for ($index = 0; $index < self::OPINION_NB_TUPLE; $index++) {
            // $opinionData = [$index, $author, $reduction];
            $opinionData[] = [
                $this->getReference(
                    UserFixture::USER_REFERENCE.random_int(
                        0,
                        UserFixture::USER_NB_TUPLE - 1
                    )
                ),
                $this->getReference(
                    ReductionFixture::REDUCTION_REFERENCE.random_int(
                        0,
                        ReductionFixture::REDUCTION_NB_TUPLE - 1
                    )
                ),
            ];
        }

        return $opinionData;
    }

    /**
     * Get one dependency to create Opinion data.
     */
    public function getDependencies(): array
    {
        return [ReductionFixture::class];
    }
}
