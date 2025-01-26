<?php

namespace App\Tests\Integration\GraphQL;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Trade;
use App\Entity\Transaction;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use JsonException;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class GraphQLTest extends ApiTestCase
{
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel(['environment' => 'test']);
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
    }

    /**
     * @param string $query
     * @return array
     * @throws ClientExceptionInterface
     * @throws JsonException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function executeGraphQLQuery(string $query): array
    {
        $variables = [];
        $client = static::createClient();

        $response = $client->request('POST', '/api/graphql', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => ['query' => $query, 'variables' => $variables],
        ]);

        self::assertResponseIsSuccessful();

        return json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @return void
     * @throws ClientExceptionInterface
     * @throws JsonException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testFetchTrades(): void
    {
        $trade = new Trade();
        $trade->setDate(new DateTime('2024-01-07'));
        $trade->setNote('Note for trade: 7');
        $this->entityManager->persist($trade);
        $this->entityManager->flush();

        $query = <<<GRAPHQL
        query {
            trades {
                edges {
                    node {
                        id
                        number
                        date
                        note
                    }
                }
            }
        }
        GRAPHQL;

        $responseData = $this->executeGraphQLQuery($query);

        $this->assertArrayHasKey('data', $responseData);
        $this->assertArrayHasKey('trades', $responseData['data']);
        $this->assertSame('2024-01-07', $responseData['data']['trades']['edges'][0]['node']['date']);
        $this->assertSame('Note for trade: 7', $responseData['data']['trades']['edges'][0]['node']['note']);
    }

    /**
     * @return void
     * @throws ClientExceptionInterface
     * @throws JsonException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testFetchTransactions(): void
    {
        $trade = new Trade();
        $trade->setDate(new DateTime('2024-01-07'));
        $trade->setNote('Note for trade: 7');
        $this->entityManager->persist($trade);

        $transaction = new Transaction();
        $transaction->setTrade($trade);
        $transaction->setClientName('John Doe');
        $transaction->setPrice(1000.50);
        $transaction->setCommodity('Oil');
        $transaction->setVolume(500);
        $transaction->setType('buy');
        $this->entityManager->persist($transaction);

        $this->entityManager->flush();

        $query = <<<GRAPHQL
        query {
            transactions {
                edges {
                    node {
                        id
                        trade {
                            id
                            number
                        }
                        clientName
                        price
                        commodity
                        volume
                        type
                    }
                }
            }
        }
        GRAPHQL;

        $responseData = $this->executeGraphQLQuery($query);

        $this->assertArrayHasKey('data', $responseData);
        $this->assertArrayHasKey('transactions', $responseData['data']);
        $this->assertNotEmpty($responseData['data']['transactions']['edges']);

        $transactionNode = $responseData['data']['transactions']['edges'][0]['node'];
        $this->assertSame('John Doe', $transactionNode['clientName']);
        $this->assertSame(1000.50, $transactionNode['price']);
        $this->assertSame('Oil', $transactionNode['commodity']);
        $this->assertSame(500, $transactionNode['volume']);
        $this->assertSame('buy', $transactionNode['type']);
    }

    /**
     * @return void
     * @throws ClientExceptionInterface
     * @throws JsonException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testCreateTradeAndTransaction(): void
    {
        $mutation = <<<GRAPHQL
        mutation {
            createTrade(input: {
                date: "2024-01-07",
                note: "Note for trade: 7"
            }) {
                trade {
                    id
                    number
                    date
                    note
                }
            }
        }
        GRAPHQL;

        $responseData = $this->executeGraphQLQuery($mutation);

        $this->assertArrayHasKey('data', $responseData);
        $this->assertArrayHasKey('createTrade', $responseData['data']);
        $this->assertArrayHasKey('trade', $responseData['data']['createTrade']);

        $trade = $responseData['data']['createTrade']['trade'];
        $this->assertSame('2024-01-07', $trade['date']);
        $this->assertSame('Note for trade: 7', $trade['note']);

        $tradeId = $trade['id'];

        $mutation = <<<GRAPHQL
        mutation {
            createTransaction(input: {
                trade: "$tradeId",
                clientName: "John Doe",
                price: 1000.50,
                commodity: "Oil",
                volume: 500,
                type: "buy"
            }) {
                transaction {
                    id
                    trade {
                        id
                        number
                    }
                    clientName
                    price
                    commodity
                    volume
                    type
                }
            }
        }
        GRAPHQL;

        $responseData = $this->executeGraphQLQuery($mutation);

        $this->assertArrayHasKey('data', $responseData);
        $this->assertArrayHasKey('createTransaction', $responseData['data']);
        $this->assertArrayHasKey('transaction', $responseData['data']['createTransaction']);

        $transaction = $responseData['data']['createTransaction']['transaction'];
        $this->assertSame('John Doe', $transaction['clientName']);
        $this->assertSame(1000.50, $transaction['price']);
        $this->assertSame('Oil', $transaction['commodity']);
        $this->assertSame(500, $transaction['volume']);
        $this->assertSame('buy', $transaction['type']);
    }
}
