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
    private EntityManagerInterface $entityManager;

    // Injection de EntityManagerInterface via le constructeur
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
    public function add(Request $request): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Associer l'utilisateur connecté comme auteur de la recette
            $recipe->setAuthor($this->getUser());

            // Utiliser l'entity manager pour persister la recette
            $this->entityManager->persist($recipe);
            $this->entityManager->flush();

            // Rediriger vers la page d'accueil après l'ajout
            return $this->redirectToRoute('app_home');
        }

        return $this->render('recipe/add_recipe.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
