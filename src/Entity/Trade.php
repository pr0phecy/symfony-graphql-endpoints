<?php

namespace App\Entity;

use Ramsey\Uuid\Uuid;
use Random\RandomException;
use Doctrine\DBAL\Types\Types;
use Ramsey\Uuid\UuidInterface;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\TradeRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GraphQl\Mutation;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use ApiPlatform\Metadata\GraphQl\QueryCollection;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    graphQlOperations: [
        new QueryCollection(
            description: 'Fetch a collection of trades',
            name: 'collection_query'
        ),
        new Mutation(
            description: 'Create a new trade',
            name: 'create'
        ),
    ]
)]
#[ORM\Table(name: 'trade')]
#[HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: TradeRepository::class)]
class Trade
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private UuidInterface $id;

    #[ORM\Column(type: Types::STRING, length: 10, unique: true, nullable: false)]
    private string $number;

    #[ORM\Column(type: Types::DATE_MUTABLE, length: 255, nullable: false)]
    private \DateTimeInterface $date;

    #[ORM\OneToMany(targetEntity: Transaction::class, mappedBy: 'trade')]
    private Collection $transactions;

    #[Assert\Length(
        max: 255,
        maxMessage: 'The note cannot be longer than {{ limit }} characters.'
    )]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private string $note;

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

    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * @throws RandomException
     */
    #[ORM\PrePersist]
    public function setNumber(): void
    {
        $this->number = bin2hex(random_bytes(5));
    }

    public function getDate(): string
    {
        return $this->date->format('Y-m-d');
    }

    public function setDate(\DateTimeInterface $date): void
    {
        $this->date = $date;
    }

    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function getNote(): string
    {
        return $this->note;
    }

    public function setNote(string $note): void
    {
        $this->note = $note;
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
