<?php

declare(strict_types = 1);

namespace App\Tests\Form\DataTransformer;

use PHPUnit\Framework\TestCase;
use App\Form\DataTransformer\ArrayToStringTransformer;

/**
 * @group   Unit
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class ArrayToStringTransformerTest extends TestCase
{
    /** @var ArrayToStringTransformer */
    private $transformer;

    protected function setUp(): void
    {
        $this->transformer = new ArrayToStringTransformer();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->transformer = null;
    }

    public function testTransformMethodReturningTheRightStringAccordingToThePassedArray(): void
    {
        $this->assertSame(
            'ROLE_USER, ROLE_ADMIN, ROLE_SUPER_ADMIN',
            $this->transformer->transform(['ROLE_USER', 'ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])
        );
    }

    public function testTransformMethodReturningAnEmptyStringForAnEmptyArray(): void
    {
        $this->assertSame('', $this->transformer->transform([]));
    }

    /**
     * Ensures that strings (such as User roles) are created correctly.
     */
    public function testReverseTransformMethodReturningTheRightAmountOfIndexes(): void
    {
        $this->assertCount(
            3,
            $this->transformer->reverseTransform('ROLE_USER, ROLE_ADMIN, ROLE_SUPER_ADMIN')
        );
        $this->assertSame(
            'ROLE_USER',
            $this->transformer->reverseTransform('ROLE_USER, ROLE_ADMIN, ROLE_SUPER_ADMIN')[0]
        );
    }

    public function testReverseTransformMethodReturningTheRightAmountOfStringsWithTooManyCommas(): void
    {
        $this->assertCount(3, $this->transformer->reverseTransform('ROLE_USER, ROLE_ADMIN,, ROLE_SUPER_ADMIN,'));
    }

    public function testReverseTransformMethodReturningAnEmptyArrayForAnEmptyString(): void
    {
        $this->assertSame([], $this->transformer->reverseTransform(''));
    }
}
