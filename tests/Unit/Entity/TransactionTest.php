<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Transaction;
use App\Entity\Trade;
use DateTimeImmutable;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class TransactionTest extends TestCase
{
    /**
     * @return void
     */
    public function testConstructorGeneratesRandomUuid(): void
    {
        $transaction = new Transaction();

        self::assertNotEmpty($transaction->getId()->toString());
        self::assertTrue(Uuid::isValid($transaction->getId()->toString()));
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testSetAndGetTrade(): void
    {
        $trade = $this->createMock(Trade::class);
        $transaction = new Transaction();
        $transaction->setTrade($trade);

        self::assertSame($trade, $transaction->getTrade());
    }

    /**
     * @return void
     */
    public function testSetAndGetClientName(): void
    {
        $transaction = new Transaction();
        $transaction->setClientName('John Doe');

        self::assertSame('John Doe', $transaction->getClientName());
    }

    /**
     * @return void
     */
    public function testSetAndGetPrice(): void
    {
        $transaction = new Transaction();
        $transaction->setPrice(100.50);

        self::assertSame(100.50, $transaction->getPrice());
    }

    /**
     * @return void
     */
    public function testSetAndGetCommodity(): void
    {
        $transaction = new Transaction();
        $transaction->setCommodity('Oil');

        self::assertSame('Oil', $transaction->getCommodity());
    }

    /**
     * @return void
     */
    public function testSetAndGetVolume(): void
    {
        $transaction = new Transaction();
        $transaction->setVolume(500);

        self::assertSame(500, $transaction->getVolume());
    }

    /**
     * @return void
     */
    public function testSetAndGetType(): void
    {
        $transaction = new Transaction();
        $transaction->setType('buy');

        self::assertSame('buy', $transaction->getType());
    }

    /**
     * @return void
     */
    public function testSetAndGetCreatedAt(): void
    {
        $transaction = new Transaction();
        $transaction->setCreatedAt();

        self::assertNotNull($transaction->getCreatedAt());
        self::assertInstanceOf(DateTimeImmutable::class, $transaction->getCreatedAt());
    }

    /**
     * @return void
     */
    public function testSetAndGetUpdatedAt(): void
    {
        $transaction = new Transaction();
        $transaction->setUpdatedAt();

        self::assertNotNull($transaction->getUpdatedAt());
        self::assertInstanceOf(DateTimeImmutable::class, $transaction->getUpdatedAt());
    }

    /**
     * @return void
     */
    public function testSetAndGetDeletedAt(): void
    {
        $deletedAt = new DateTimeImmutable();
        $transaction = new Transaction();
        $transaction->setDeletedAt($deletedAt);

        self::assertSame($deletedAt, $transaction->getDeletedAt());
    }
}
