<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Trade;
use App\Entity\Transaction;
use Random\RandomException;
use App\Service\EntityManager;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class Fixtures extends Fixture
{
    public function __construct(
        private readonly EntityManager $entityManager,
    ) {
    }

    /**
     * @param ObjectManager $manager
     * @return void
     * @throws RandomException
     * @throws \Exception
     */
    public function load(ObjectManager $manager): void
    {
        $trades = $this->loadTrades();

        foreach ($trades as $trade) {
            $this->entityManager->persist($trade);
        }

        $this->loadTransactions($trades);

        $manager->flush();
    }

    /**
     * @return array
     * @throws \Exception
     */
    private function loadTrades(): array
    {
        $trades = [];

        for ($i = 1; $i <= 10; ++$i) {
            $trade = new Trade();

            $trade->setDate(new \DateTimeImmutable(sprintf('2024-01-%02d', $i)));
            $trade->setNote('Note for trade: ' . $i);

            $trades[] = $trade;
        }

        return $trades;
    }

    /**
     * @param array $trades
     * @return void
     * @throws RandomException
     */
    private function loadTransactions(array $trades): void
    {
        foreach ($trades as $trade) {
            for ($i = 1; $i <= 5; ++$i) {
                $transaction = new Transaction();
                $transaction->setTrade($trade);
                $transaction->setClientName('Client: ' . $i);
                $transaction->setPrice(random_int(100, 1000));
                $transaction->setCommodity('Oil: ' . $i);
                $transaction->setVolume(random_int(1, 100));
                $transaction->setType(['buy', 'sell'][random_int(0, 1)]);

                $this->entityManager->persist($transaction);
            }
        }
    }
}
