<?php

/**
 * OpinionFixture file
 *
 * @category    Opinion
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */

namespace App\DataFixtures;

use App\Entity\Opinion;
use Faker;
use App\Service\GlobalClock;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @see         https://symfony.com/doc/master/bundles/DoctrineFixturesBundle/index.html
 */
final class OpinionFixture extends Fixture implements DependentFixtureInterface
{
    /**
     * @var int public CONST for Opinions number in DB
     */
    public const OPINION_NB_TUPLE = 40;

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
     * ReductionFixture constructor.
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
     * Load OPINION_NB_TUPLE opinions to DB
     *
     * @link https://github.com/fzaninotto/Faker
     * @see 3 Loop iterator depends on const OPINION_NB_TUPLE
     * @param ObjectManager $manager Doctrine Manager
     *
     * @return void
     * @throws \Exception Datetime Exception
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create($this->container->getParameter('faker_locale'));

        foreach ($this->getOpinionReferenceData() as [$index, $author, $reduction]) {
            $opinion = new Opinion();
            $opinion->setUser($author);
            $opinion->setReduction($reduction);
            $opinion->setName($faker->userName);
            $opinion->setEmail($faker->email);
            $opinion->setClientIp($faker->ipv4);
            $opinion->setComment($faker->realText(100));
            $opinion->setCreationDate($this->clock->getNowInDateTime());

            $manager->persist($opinion);
        }

        $manager->flush();
    }

    /**
     * Get an array of references useful for Opinion instances
     *
     * @see 9 See UserFixture::USER_NB_TUPLE - 1 for index 0
     * @see 15 See ReductionFixture::REDUCTION_NB_TUPLE - 1 for index 0
     *
     * @return array
     * @throws \Exception Random Exception
     */
    private function getOpinionReferenceData(): array
    {
        $opinionData = [];
        for ($index = 0; $index < self::OPINION_NB_TUPLE; $index++) {
            // $opinionData = [$index, $author, $reduction];
            $opinionData[] = [
                $index,
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
     * Get one dependency to create Opinion data
     *
     * @return array
     */
    public function getDependencies(): array
    {
        return [
            ReductionFixture::class,
        ];
    }
}
