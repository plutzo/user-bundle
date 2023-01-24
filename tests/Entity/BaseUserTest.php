<?php

declare(strict_types=1);


namespace Marlinc\UserBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use Marlinc\UserBundle\Entity\BaseUser;

final class BaseUserTest extends TestCase
{
    public function testDateSetters(): void
    {
        // Given
        $user = new BaseUser();
        $today = new \DateTime();

        // When
        $user->setCreatedAt($today);
        $user->setUpdatedAt($today);

        // Then
        $createdAt = $user->getCreatedAt();
        $updatedAt = $user->getUpdatedAt();

        static::assertInstanceOf(\DateTime::class, $createdAt, 'Should return a DateTime object');
        static::assertSame($today->format('U'), $createdAt->format('U'), 'Should contain today\'s date');

        static::assertInstanceOf(\DateTime::class, $updatedAt, 'Should return a DateTime object');
        static::assertSame($today->format('U'), $updatedAt->format('U'), 'Should contain today\'s date');
    }

    public function testDateWithPrePersist(): void
    {
        // Given
        $user = new BaseUser();
        $today = new \DateTime();

        // When
        $user->prePersist();

        // Then
        $createdAt = $user->getCreatedAt();
        $updatedAt = $user->getUpdatedAt();

        static::assertInstanceOf(\DateTime::class, $createdAt, 'Should contain a DateTime object');
        static::assertSame($today->format('Y-m-d'), $createdAt->format('Y-m-d'), 'Should be created today');

        static::assertInstanceOf(\DateTime::class, $updatedAt, 'Should contain a DateTime object');
        static::assertSame($today->format('Y-m-d'), $updatedAt->format('Y-m-d'), 'Should be updated today');
    }

    public function testDateWithPreUpdate(): void
    {
        // Given
        $date = \DateTime::createFromFormat('Y-m-d', '2012-01-01');
        \assert(false !== $date);

        $user = new BaseUser();
        $user->setCreatedAt($date);
        $today = new \DateTime();

        // When
        $user->preUpdate();

        // Then
        $createdAt = $user->getCreatedAt();
        $updatedAt = $user->getUpdatedAt();

        static::assertInstanceOf(\DateTime::class, $createdAt, 'Should contain a DateTime object');
        static::assertSame('2012-01-01', $createdAt->format('Y-m-d'), 'Should be created at 2012-01-01.');

        static::assertInstanceOf(\DateTime::class, $updatedAt, 'Should contain a DateTime object');
        static::assertSame($today->format('Y-m-d'), $updatedAt->format('Y-m-d'), 'Should be updated today');
    }

    public function testToStringWithName(): void
    {
        // Given
        $user = new BaseUser();
        $user->setEmail('john@example.com');

        // When
        $string = (string) $user;

        // Then
        static::assertSame('john@example.com', $string, 'Should return the username as string representation');
    }

    public function testToStringWithoutName(): void
    {
        // Given
        $user = new BaseUser();

        // When
        $string = (string) $user;

        // Then
        static::assertSame('-', $string, 'Should return a string representation');
    }
}
