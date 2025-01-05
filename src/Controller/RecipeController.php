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
