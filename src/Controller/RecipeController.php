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
    
    private $entityManager;

    // Injection de l'EntityManager dans le contrôleur
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
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
    #[Route('/recipes', name: 'app_all_recipes')]
    public function allRecipes(): Response
    {
        $recipes = $this->entityManager->getRepository(Recipe::class)->findAll();
    
        return $this->render('recipe/all_recipes.html.twig', [
            'recipes' => $recipes,
        ]);
    }
    
     

    #[Route("/recipe/{id}/edit", name:"recipe_edit")]
    public function edit(Request $request, EntityManagerInterface $em, $id)
    {
        // Récupérer la recette à modifier
        $recipe = $em->getRepository(Recipe::class)->find($id);
        
        if (!$recipe) {
            throw $this->createNotFoundException('Recette non trouvée');
        }

        // Créer le formulaire de modification
        $form = $this->createForm(RecipeType::class, $recipe);
        
        // Traitement du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarde de la recette modifiée
            $em->flush();

            // Message flash pour indiquer le succès de la modification
            $this->addFlash('success', 'Recette modifiée avec succès!');
            return $this->redirectToRoute('app_all_recipes'); // Redirection vers la liste des recettes ou une autre page
        }

        // Affichage du formulaire
        return $this->render('recipe/edit.html.twig', [
            'form' => $form->createView(),
        ]);
        
    }
    


}
