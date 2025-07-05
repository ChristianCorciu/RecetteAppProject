<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class UserRecipeView
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private $user;

    #[ORM\ManyToOne(targetEntity: Recipe::class)]
    private $recipe;

    #[ORM\Column(type: 'datetime')]
    private $viewedAt;

    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getUser(): ?User
    {
        return $this->user;
    }
    
    public function setUser(?User $user): self
    {
        $this->user = $user;
    
        return $this;
    }
    
    public function getRecipe(): ?Recipe
    {
        return $this->recipe;
    }
    
    public function setRecipe(?Recipe $recipe): self
    {
        $this->recipe = $recipe;
    
        return $this;
    }
    
    public function getViewedAt(): ?\DateTimeInterface
    {
        return $this->viewedAt;
    }
    
    public function setViewedAt(\DateTimeInterface $viewedAt): self
    {
        $this->viewedAt = $viewedAt;
    
        return $this;
    }
    
}
