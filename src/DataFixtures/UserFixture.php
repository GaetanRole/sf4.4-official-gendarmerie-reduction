<?php

declare(strict_types=1);

namespace App\DataFixtures;

use \Exception;
use App\Entity\User;
use App\Service\GlobalClock;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Ramsey\Uuid\Uuid;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @see     https://symfony.com/doc/master/bundles/DoctrineFixturesBundle/index.html
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class UserFixture extends Fixture implements FixtureGroupInterface
{
    /** @var int public CONST for Users number in DB */
    public const USER_NB_TUPLE = 20;

    /** @var string public CONST for reference used in ReductionFixture, concat to an int [0-USER_NB_TUPLE] */
    public const USER_REFERENCE = 'user-';

    /**
     * To encode password with injected service.
     *
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

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
     * Load USER_NB_TUPLE Users to DB.
     *
     * @see     10 See USER_NB_TUPLE to know iterator value
     * @see    https://github.com/fzaninotto/Faker
     *
     * @throws Exception Datetime Exception
     */
    public function load(ObjectManager $manager): void
    {
        // Loading USER_NB_TUPLE users with information by concat
        // E.g : Login : user0
        //     : Password : password0

        $faker = Faker\Factory::create($this->container->getParameter('faker_locale'));
        $roles = ['ROLE_USER', 'ROLE_ADMIN', 'ROLE_SUPER_ADMIN'];

        for ($index = 0; $index < self::USER_NB_TUPLE; ++$index) {
            $user = new User();

            $user->setUuid(Uuid::uuid4());
            $user->setCreatedAt($this->clock->getNowInDateTime());
            $user->setUpdatedAt(null);

            $user->setUsername('user'.$index)
                ->setIdentity($this->getRandomIdentity())
                ->setEmail($faker->email)
                ->setPassword($this->passwordEncoder->encodePassword($user, 'password'.$index))
                ->setPhoneNumber($faker->phoneNumber)
                ->setIsActive(true)
                ->setRoles([$roles[array_rand($roles)]])
                ->setAvatar($this->getARandomAvatar($user->hasRole('ROLE_SUPER_ADMIN')))
            ;

            $manager->persist($user);
            $this->addReference(self::USER_REFERENCE.$index, $user);
        }

        $manager->flush();
    }

    /**
     * Get a random identity.
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
     * Get a random avatar, except for an admin.
     */
    private function getARandomAvatar(bool $isSuperAdmin): string
    {
        if ($isSuperAdmin) {
            return 'super-admin-avatar.png';
        }

        $avatars = [
            'user-avatar-cat.png',
            'user-avatar-elephant.png',
            'user-avatar-fox.png',
            'user-avatar-monkey.png',
            'user-avatar-panda.png',
            'user-avatar-penguin.png',
            'user-avatar-rabbit.png',
        ];

        return $avatars[array_rand($avatars)];
    }

    /**
     * Get User / Brand / Category fixtures.
     */
    public static function getGroups(): array
    {
        return ['independent'];
    }
}
