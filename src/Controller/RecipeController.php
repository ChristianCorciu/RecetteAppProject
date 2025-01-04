<?php

namespace App\Controller;

use App\Entity\Recipe; // Assure-toi que cet import est présent
use App\Form\RecipeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class RecipeController extends AbstractController
{
    #[Route('/recipe', name: 'app_recipe')]
    public function index(): Response
    {
        return $this->render('recipe/index.html.twig', [
            'controller_name' => 'RecipeController',
        ]);
    }

    #[Route('/add-recipe', name: 'app_add_recipe')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Associe l'utilisateur connecté comme auteur
            $recipe->setAuthor($this->getUser());

            // Sauvegarde des ingrédients avec leur quantité
            foreach ($recipe->getRecipeIngredients() as $recipeIngredient) {
                $em->persist($recipeIngredient);
            }

            $em->persist($recipe);
            $em->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('recipe/add_recipe.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
