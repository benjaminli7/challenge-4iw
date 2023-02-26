<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use App\Repository\ArticleRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\HasLifecycleCallbacks]

class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]

    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: "category",targetEntity: Article::class, cascade: ["persist"])]
    private Collection $articles;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
    }

    #[ORM\PreRemove]
    public function checkForOrders(): void
    {
        //check if there are orders for one of the articles in this category

        foreach ($this->articles as $article) {
            if ($article->getOrders()->count() > 0) {
                throw new \Exception("One or more articles in this category have order");
            }
        }
    }

    public function getArticles(): Collection
    {
        return $this->articles;
    }
    public function getId(): ?int
    {
        return $this->id;
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
}
