<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use App\Repository\ReviewRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReviewRepository::class)]
class Review
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\Range(min: 1, max: 5)]
    private ?int $note = null;

    #[ORM\Column(length: 1000)]
    private ?string $comment = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'reviews')]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false)]
    private ?User $user = null;

    // approved by admin or not
    #[ORM\Column]
    private bool $approved = false;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(int $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function isApproved(): bool
    {
        return $this->approved;
    }

    public function setApproved(bool $approved): self
    {
        $this->approved = $approved;
        return $this;
    }

    public function setUser($user): self
    {
        $this->user = $user;
        return $this;

    }

    public function getUser() : ?User
    {
        return $this->user;
    }
}
