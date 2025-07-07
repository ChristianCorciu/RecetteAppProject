<?php

// src/Controller/RecipeController.php
namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Service\SpoonacularService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\RecipeRepository;


class RecipeController extends AbstractController
{
    private $entityManager;
    private $spoonacularService;

    // Injection de l'EntityManager et du service Spoonacular dans le contrôleur
    public function __construct(EntityManagerInterface $entityManager, SpoonacularService $spoonacularService)
    {
        $this->entityManager = $entityManager;
        $this->spoonacularService = $spoonacularService;
    }

    #[Route('/recipe', name: 'app_recipe')]
    public function index(): Response
    {
        return $this->render('recipe/index.html.twig', [
            'controller_name' => 'RecipeController',
        ]);
    }

    #[Route("/recipe/{id}", name: "recipe_view")]
    public function view(EntityManagerInterface $em, $id): Response
    {
        // Récupérer la recette à afficher
        $recipe = $em->getRepository(Recipe::class)->find($id);

        if (!$recipe) {
            throw $this->createNotFoundException('Recette non trouvée');
        }

        // Afficher la recette complète
        return $this->render('recipe/view.html.twig', [
            'recipe' => $recipe,
        ]);
    }

    // src/Controller/RecipeController.php

#[Route("/recipe/{id}/spoonacular", name: "recipe_spoonacular")]
public function spoonacularDetails($id): Response
{
    // Utiliser le service Spoonacular pour récupérer les détails d'une recette
    $recipeDetails = $this->spoonacularService->getRecipeDetails($id);

    // Vérifier si 'recipeDetails' contient les informations nutritionnelles
    $nutrition = $recipeDetails['nutrition'] ?? [];

    // Si les clés existent, les assigner
    $fiber = $nutrition['fiber'] ?? 'Non disponible';
    $calories = $nutrition['calories'] ?? 'Non disponible';
    $carbs = $nutrition['carbs'] ?? 'Non disponible';
    $protein = $nutrition['protein'] ?? 'Non disponible';
    $fat = $nutrition['fat'] ?? 'Non disponible';

    // Passer les données à la vue
    return $this->render('recipe/spoonacular_details.html.twig', [
        'recipe' => $recipeDetails,  // Passer 'recipeDetails' à la vue
        'fiber' => $fiber,
        'calories' => $calories,
        'carbs' => $carbs,
        'protein' => $protein,
        'fat' => $fat,
    ]);
}


#[Route("/recipe/{id}/nutrition", name: "recipe_nutrition")]
public function nutrition(int $id): Response
{
    // Récupérer la recette
    $recipe = $this->entityManager->getRepository(Recipe::class)->find($id);
    if (!$recipe) {
        throw $this->createNotFoundException('Recette non trouvée');
    }

    // Utiliser le service Spoonacular pour récupérer les informations nutritionnelles
    $nutrition = $this->spoonacularService->getRecipeNutrition($id);

    // Vérifier la présence des variables nutritionnelles
    $calories = isset($nutrition['calories']) ? $nutrition['calories'] : null;
    $protein = isset($nutrition['protein']) ? $nutrition['protein'] : null;
    $fat = isset($nutrition['fat']) ? $nutrition['fat'] : null;
    $carbs = isset($nutrition['carbs']) ? $nutrition['carbs'] : null;
    $sugar = isset($nutrition['sugar']) ? $nutrition['sugar'] : null;
    $fiber = isset($nutrition['fiber']) ? $nutrition['fiber'] : null;

    // Passer toutes les variables nécessaires à la vue
    return $this->render('recipe/spoonacular_details.html.twig', [
        'recipe' => $recipe,
        'nutrition' => $nutrition,
        'calories' => $calories,
        'protein' => $protein,
        'fat' => $fat,
        'carbs' => $carbs,
        'sugar' => $sugar,
        'fiber' => $fiber,
    ]);
}




    #[Route('/add-recipe', name: 'app_add_recipe')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Si les étapes sont envoyées comme un tableau JSON
            $steps = $form->get('steps')->getData();  // Récupérer les données du champ steps comme tableau
            $recipe->setSteps($steps);  // Assurez-vous que vous avez bien configuré le setter

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
    #[Route('/recettes/recherche', name: 'recipe_search')]
    public function search(Request $request, RecipeRepository $recipeRepository): Response
    {
        $query = $request->query->get('q', '');
        $recipes = $recipeRepository->findByName($query);

        return $this->render('recipe/all_recipes.html.twig', [
            'recipes' => $recipes
        ]);
    }


    #[Route("/recipe/{id}/delete", name: "recipe_delete")]
    public function delete(EntityManagerInterface $em, $id): Response
    {
        // Récupérer la recette à supprimer
        $recipe = $em->getRepository(Recipe::class)->find($id);

        if (!$recipe) {
            throw $this->createNotFoundException('Recette non trouvée');
        }

        // Vérifier si l'utilisateur connecté est bien l'auteur de la recette
        if ($recipe->getAuthor() !== $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer cette recette.');
            return $this->redirectToRoute('app_home'); // Rediriger vers la liste des recettes ou une autre page
        }

        // Suppression de la recette
        $em->remove($recipe);
        $em->flush();

        // Message flash pour indiquer le succès de la suppression
        $this->addFlash('success', 'Recette supprimée avec succès!');
        
        return $this->redirectToRoute('app_home'); // Redirection vers la liste des recettes ou une autre page
    }

    #[Route("/recipe/{id}/edit", name: "recipe_edit")]
    public function edit(Request $request, EntityManagerInterface $em, $id): Response
    {
        $recipe = $em->getRepository(Recipe::class)->find($id);

        if (!$recipe) {
            throw $this->createNotFoundException('Recette non trouvée');
        }

        if ($recipe->getAuthor() !== $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas modifier cette recette.');
            return $this->redirectToRoute('app_all_recipes');
        }

        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les étapes depuis le formulaire et les définir comme un tableau JSON
            $steps = $form->get('steps')->getData();
            $recipe->setSteps($steps);

            $em->flush();

            $this->addFlash('success', 'Recette modifiée avec succès!');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('recipe/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}


