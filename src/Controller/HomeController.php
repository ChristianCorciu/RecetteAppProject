<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(RecipeRepository $recipeRepository): Response
    {
         // Récupérer toutes les recettes
         $recipes = $recipeRepository->findAll();

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

