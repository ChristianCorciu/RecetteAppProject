<?php
namespace App\Entity;

use App\Repository\UserProfileRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserProfileRepository::class)]
class UserProfile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\OneToOne(inversedBy: 'profile', targetEntity: User::class, cascade: ['persist', 'remove'])]
    private $user;

    #[ORM\Column(type: 'text', nullable: true)]
    private $bio;

    #[ORM\Column(type: 'string', length: 50)]
    private $level; // Débutant, Intermédiaire, Chef

    // Getters / Setters...
}
?>