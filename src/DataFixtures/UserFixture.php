<?php

/**
 * UserFixture file
 *
 * PHP Version 7.2
 *
 * @category    User
 * @package     App\DataFixtures
 * @version     1.0
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */

namespace App\DataFixtures;

use App\Entity\User;
use Faker;
use App\Service\GlobalClock;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * UserFixture class
 *
 * @see         https://symfony.com/doc/master/bundles/DoctrineFixturesBundle/index.html
 * @category    User
 * @package     App\DataFixtures
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class UserFixture extends Fixture implements FixtureGroupInterface
{
    /**
     * @var int public CONST for Users number in DB
     */
    public const USER_NB_TUPLE = 20;

    /**
     * @var string public CONST for reference used in ReductionFixture, concat to an int [0-USER_NB_TUPLE]
     */
    public const USER_REFERENCE = 'user-';

    /**
     * To encode password with injected service
     *
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

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
     * UserFixture constructor.
     *
     * @link https://github.com/Innmind/TimeContinuum Global clock
     * @param UserPasswordEncoderInterface $passwordEncoder Var to encode password
     * @param GlobalClock $clock Global project's clock
     * @param ContainerInterface $container Container Interface
     */
    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        GlobalClock $clock,
        ContainerInterface $container
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->clock = $clock;
        $this->container = $container;
    }

    /**
     * Load USER_NB_TUPLE Users to DB
     *
     * @link https://github.com/fzaninotto/Faker
     * @see 10 See USER_NB_TUPLE to know iterator value
     * @param ObjectManager $manager Doctrine Manager
     *
     * @return void
     * @throws \Exception Datetime Exception
     */
    public function load(ObjectManager $manager): void
    {
        // Loading USER_NB_TUPLE users with information by concat
        // Enter a \DateTime(now) by TimeContinuum service
        // E.g : Login : $faker->userName
        //     : Password : password0

        $faker = Faker\Factory::create($this->container->getParameter('faker_locale'));
        $roles = ['ROLE_USER', 'ROLE_ADMIN', 'ROLE_SUPER_ADMIN'];

        for ($index = 0; $index < self::USER_NB_TUPLE; $index++) {
            $user = new User();
            $user
                ->setUsername($faker->userName)
                ->setIdentity($this->getRandomIdentity())
                ->setEmail($faker->email)
                ->setPassword(
                    $this->passwordEncoder->encodePassword(
                        $user,
                        'password' . $index
                    )
                )
                ->setPhoneNumber($faker->phoneNumber)
                ->setIsActive(true)
                ->setCreationDate($this->clock->getNowInDateTime());
            $user->setRoles([$roles[array_rand($roles)]]);

            $manager->persist($user);
            $this->addReference(self::USER_REFERENCE.$index, $user);
        }

        $manager->flush();
    }

    /**
     * Get a random identity
     *
     * @return string
     */
    private function getRandomIdentity(): string
    {
        $identities = [
            'Administrateur Michel R.',
            'Utilisateur par défaut',
            'Modérateur Jean P.',
            'Visiteur',
            'Administrateur Thomas R.',
        ];

        return $identities[array_rand($identities)];
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
