<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;


class HomeController extends AbstractController
{

    #[Route('/', name: 'app_landing')]
    public function landing(Security $security): Response
    {
        if ($security->getUser()) {
            return $this->redirectToRoute('app_home');
        }
    
        return $this->render('landing.html.twig');
    }
    
    #[Route('/home', name: 'app_home')]
    public function index( RecipeRepository $recipeRepository,
    Security $security
): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!$security->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('app_landing');
        }

         // Récupérer l'utilisateur connecté
         // Vérifier si l'utilisateur est connecté      
    {
         {
    $user = $security->getUser();
         // Récupérer toutes les recettes
         $recipes = $recipeRepository->findBy(['author' => $user]);

         // Séparer les recettes en catégories
         $entrées = array_filter($recipes, fn($r) => $r->getCategory()->getName() === 'Entrées');
         $plats = array_filter($recipes, fn($r) => $r->getCategory()->getName() === 'Plats');
         $desserts = array_filter($recipes, fn($r) => $r->getCategory()->getName() === 'Desserts');
 
         return $this->render('home/index.html.twig', [
             'entrées' => $entrées,
             'plats' => $plats,
             'desserts' => $desserts,
         ]);
    }
}
    }
}
