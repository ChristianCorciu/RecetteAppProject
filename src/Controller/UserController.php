<?php
// src/Controller/UserController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractController
{
    public function profile(string $email, string $name): Response
    {
        // Logique pour afficher la page avec l'email et le nom
        return $this->render('user/profile.html.twig', [
            'email' => $email,
            'name' => $name,
        ]);
    }
}
