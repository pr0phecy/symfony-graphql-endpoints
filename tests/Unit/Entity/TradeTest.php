<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Trade;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;
use Random\RandomException;

final class TradeTest extends TestCase
{
    public function testConstructorGeneratesRandomUuid(): void
    {
        $trade = new Trade();

        self::assertInstanceOf(UuidInterface::class, $trade->getId());
        self::assertNotEmpty($trade->getId()->toString());
    }

    /**
     * @throws RandomException
     */
    public function testSetAndGetNumber(): void
    {
        $trade = new Trade();
        $trade->setNumber();

        self::assertMatchesRegularExpression('/^[a-f0-9]{10}$/', $trade->getNumber());
    }

    public function testSetAndGetDate(): void
    {
        $trade = new Trade();
        $date = new DateTimeImmutable('2024-01-01');

        $trade->setDate($date);

        self::assertSame('2024-01-01', $trade->getDate());
    }

    public function testSetAndGetNote(): void
    {
        $trade = new Trade();
        $note = 'This is a sample note';

        $trade->setNote($note);

        self::assertSame($note, $trade->getNote());
    }

    public function testSetAndGetCreatedAt(): void
    {
        $trade = new Trade();
        $trade->setCreatedAt();

        $createdAt = $trade->getCreatedAt();
        self::assertInstanceOf(DateTimeImmutable::class, $createdAt);
        self::assertEqualsWithDelta(new DateTimeImmutable(), $createdAt, 1);
    }

    public function testSetAndGetUpdatedAt(): void
    {
        $trade = new Trade();
        $trade->setUpdatedAt();

        $updatedAt = $trade->getUpdatedAt();
        self::assertInstanceOf(DateTimeImmutable::class, $updatedAt);
        self::assertEqualsWithDelta(new DateTimeImmutable(), $updatedAt, 1);
    }

    public function testSetAndGetDeletedAt(): void
    {
        $trade = new Trade();
        $deletedAt = new DateTimeImmutable();

        $trade->setDeletedAt($deletedAt);

        self::assertSame($deletedAt, $trade->getDeletedAt());
    }
}
