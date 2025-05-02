<?php
// src/Controller/UserController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Repository\FavoriteRepository;


class UserController extends AbstractController
{
    #[Route('/mon-profil', name: 'app_user_profile')]
    public function profile(): Response
    {
        $user = $this->getUser(); // récupère l'utilisateur connecté
    
        if (!$user) {
            return $this->redirectToRoute('app_login'); // redirige si non connecté
        }
    
        return $this->render('user/profile.html.twig', [
            'email' => $user->getEmail(),
            'name' => $user->getName(),
        ]);
    }
    #[Route('/mes-favoris', name: 'app_user_favorites')]
    public function favorites(FavoriteRepository $favRepo): Response
    {
        $user = $this->getUser();
        $favorites = $favRepo->findBy(['user' => $user]);

        return $this->render('user/favorites.html.twig', [
            'favorites' => $favorites,
        ]);
    }
   

  
}
