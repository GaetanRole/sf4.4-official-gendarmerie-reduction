<?php

declare(strict_types=1);

namespace App\Tests\Security\Voter;

use \Generator;
use App\Entity\User;
use PHPUnit\Framework\TestCase;
use App\Security\Voter\UserVoter;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * @group   Unit
 * @see     https://symfony.com/doc/current/security/voters.html
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class UserVoterTest extends TestCase
{
    private function createAnUserStub(
        string $role,
        bool $isGrantedFirstExpectation,
        bool $isGrantedSecondExpectation
    ): User {
        $userStub = $this->createMock(User::class);
        $userStub
            ->method('getRoles')
            ->willReturn([$role]);
        $userStub
            ->method('hasRole')
            ->willReturnOnConsecutiveCalls($isGrantedFirstExpectation, $isGrantedSecondExpectation);

        return $userStub;
    }

    public function provideVoteCasesWithDifferentUserData(): Generator
    {
        yield 'An user can not edit an Admin' => [
            'edit',
            $this->createAnUserStub('ROLE_ADMIN', false, true),
            $this->createAnUserStub('ROLE_USER', false, true),
            Voter::ACCESS_DENIED
        ];
        yield 'A Super Admin can edit an Admin' => [
            'status',
            $this->createAnUserStub('ROLE_ADMIN', false, true),
            $this->createAnUserStub('ROLE_SUPER_ADMIN', true, false),
            Voter::ACCESS_GRANTED
        ];
        yield 'An Admin can not delete a Super Admin' => [
            'delete',
            $this->createAnUserStub('ROLE_SUPER_ADMIN', true, false),
            $this->createAnUserStub('ROLE_ADMIN', false, true),
            Voter::ACCESS_DENIED
        ];
        yield 'An User can be deleted by others' => [
            'delete',
            $this->createAnUserStub('ROLE_USER', false, false),
            $this->createAnUserStub('ROLE_ADMIN', false, true),
            Voter::ACCESS_GRANTED
        ];
    }

    /**
     * @dataProvider provideVoteCasesWithDifferentUserData
     */
    public function testVoteOnAttributeMethod(
        string $attribute,
        User $user,
        ?User $currentUser,
        int $expectedVote
    ): void {
        $voter = new UserVoter();
        $token = new UsernamePasswordToken($currentUser, '', 'key', $currentUser->getRoles());

        $this->assertSame($expectedVote, $voter->vote($token, $user, [$attribute]));
    }
}
