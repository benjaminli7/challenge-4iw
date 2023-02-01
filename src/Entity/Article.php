<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use App\Repository\ArticleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[ORM\Table(name: '`article`')]
#[UniqueEntity(fields: ['name'])]
class Article
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Cette valeur ne peut pas être vide.')]
    #[Assert\Type('string', message: 'Cette valeur doit être une chaine de caractère.')]
    private ?string $name = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Cette valeur ne peut pas être vide.')]
    #[Assert\Type('float', message: 'Cette valeur doit être un nombre ou un chiffre.')]
    private ?float $price = null;


    #[ORM\Column]
    private ?int $orderCount = 0;

    #[ORM\ManyToOne(targetEntity: Category::class)]
    #[ORM\JoinColumn(name: "category_id", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    private ?Category $category = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getOrderCount(): ?int
    {
        return $this->orderCount;
    }

    public function setOrderCount(int $orderCount): self
    {
        $this->orderCount = $orderCount;

        return $this;
    }
}
