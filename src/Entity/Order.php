<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 40)]
    private ?string $status = "ONGOING";

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "client_id", referencedColumnName: "id", nullable: false)]
    private ?User $client = null;

    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderArticle::class, cascade: ['persist'])]
    private Collection $orderArticles;

    public function __construct()
    {
        $this->orderArticles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getClient(): ?User
    {
        return $this->client;
    }

    public function setClient(User $client): self
    {
        $this->client = $client;

        return $this;
    }
    public function getOrderArticles(): Collection
    {
        return $this->orderArticles;
    }

    public function getOrderPhoneNumber(): ?string
    {
        return $this->client->getPhone();
    }
    public function addOrderArticle(OrderArticle $orderArticle): self
    {
        if (!$this->orderArticles->contains($orderArticle)) {
            $this->orderArticles[] = $orderArticle;
            $orderArticle->setOrder($this);
        }

        return $this;
    }

    public function removeOrderArticle(OrderArticle $orderArticle): self
    {
        if ($this->orderArticles->contains($orderArticle)) {
            $this->orderArticles->removeElement($orderArticle);
            if ($orderArticle->getOrder() === $this) {
                $orderArticle->setOrder(null);
            }
        }

        return $this;
    }

    public function addArticle(Article $article, int $quantity): self
    {
        $orderArticle = new OrderArticle();
        $orderArticle->setArticle($article);
        $orderArticle->setQuantity($quantity);
        $this->addOrderArticle($orderArticle);
        return $this;
    }

    public function getTotalAmount()
    {
        $total = 0;
        foreach ($this->orderArticles as $orderArticle) {
            $total += $orderArticle->getArticle()->getPrice() * $orderArticle->getQuantity();
        }
        return $total;
    }


}
