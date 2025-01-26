<?php

namespace App\Entity;

use Ramsey\Uuid\Uuid;
use Doctrine\DBAL\Types\Types;
use Ramsey\Uuid\UuidInterface;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\TransactionRepository;
use ApiPlatform\Metadata\GraphQl\Mutation;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use ApiPlatform\Metadata\GraphQl\QueryCollection;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    graphQlOperations: [
        new QueryCollection(
            description: 'Fetch a collection of transactions',
            name: 'collection_query'
        ),
        new Mutation(
            description: 'Create a new transaction',
            name: 'create',
        ),
    ]
)]
#[ORM\Table(name: 'transaction')]
#[HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private UuidInterface $id;

    #[ORM\ManyToOne(targetEntity: Trade::class, inversedBy: 'transactions')]
    #[ORM\JoinColumn(nullable: false)]
    private Trade $trade;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: false)]
    #[Assert\NotNull(message: 'The client name cannot be null.')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'The client name cannot be longer than {{ limit }} characters.'
    )]
    private string $clientName;

    #[ORM\Column(type: Types::FLOAT, nullable: false)]
    #[Assert\NotNull(message: 'The price cannot be null.')]
    #[Assert\Type(
        type: 'float',
        message: 'The price must be a valid float value.'
    )]
    #[Assert\Positive(message: 'The price must be a positive number.')]
    private float $price;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: false)]
    #[Assert\NotNull(message: 'The commodity cannot be null.')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'The commodity cannot be longer than {{ limit }} characters.'
    )]
    private string $commodity;

    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    #[Assert\NotNull(message: 'The volume cannot be null.')]
    #[Assert\Type(
        type: 'integer',
        message: 'The volume must be a valid integer.'
    )]
    #[Assert\Positive(message: 'The volume must be a positive number.')]
    private int $volume;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Assert\NotNull(message: 'The type cannot be null.')]
    #[Assert\Choice(
        choices: ['buy', 'sell'],
        message: 'The type must be one of "{{ choices }}" but "{{ value }}" was given.'
    )]
    private string $type; // Can be only "sell" or "buy"

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: false)]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: false)]
    private \DateTimeInterface $updatedAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeInterface $deletedAt;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getTrade(): Trade
    {
        return $this->trade;
    }

    public function setTrade(Trade $trade): void
    {
        $this->trade = $trade;
    }

    public function getClientName(): string
    {
        return $this->clientName;
    }

    public function setClientName(string $clientName): void
    {
        $this->clientName = $clientName;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    public function getCommodity(): string
    {
        return $this->commodity;
    }

    public function setCommodity(string $commodity): void
    {
        $this->commodity = $commodity;
    }

    public function getVolume(): int
    {
        return $this->volume;
    }

    public function setVolume(int $volume): void
    {
        $this->volume = $volume;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(): void
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->setUpdatedAt();
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    #[ORM\PreUpdate]
    public function setUpdatedAt(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeInterface $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }
}
