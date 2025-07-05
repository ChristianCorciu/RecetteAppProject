<?php
// src/Controller/FavoriteController.php

namespace App\Controller;

use App\Entity\Favorite;
use App\Repository\RecipeRepository;
use App\Repository\FavoriteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class FavoriteController extends AbstractController
{
    #[Route('/favori/ajouter/{id}', name: 'app_add_favorite')]
    public function addFavorite(
        int $id,
        RecipeRepository $recipeRepo,
        FavoriteRepository $favoriteRepo,
        EntityManagerInterface $em
    ): RedirectResponse {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $recipe = $recipeRepo->find($id);

        // Vérifie si la recette est déjà en favoris
        $existingFavorite = $favoriteRepo->findOneBy([
            'user' => $user,
            'recipe' => $recipe,
        ]);

        if (!$existingFavorite) {
            $favorite = new Favorite();
            $favorite->setUser($user);
            $favorite->setRecipe($recipe);

            $em->persist($favorite);
            $em->flush();
        }

        return $this->redirectToRoute('app_home');
    }
}
