<?php

// src/Repository/RecipeRepository.php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    // Récupérer les recettes par catégorie
    public function findByCategoryName(string $categoryName)
{
    $qb = $this->createQueryBuilder('r')
        ->innerJoin('r.category', 'c')
        ->where('c.name = :categoryName')
        ->setParameter('categoryName', $categoryName);

    $recipes = $qb->getQuery()->getResult();

    // Vérifiez le résultat
    if (empty($recipes)) {
        // Vous pouvez journaliser ou afficher le résultat pour déboguer
        dump($recipes);
    }

    return $recipes;
}}
