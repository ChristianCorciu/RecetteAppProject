<?php

// src/Service/SpoonacularService.php
namespace App\Service;

use GuzzleHttp\Client;

class SpoonacularService
{
    private $client;
    private $apiKey;

    public function __construct(string $apiKey)
    {
        $this->client = new Client();
        $this->apiKey = $apiKey;
    }

    public function searchRecipesByIngredients(array $ingredients)
    {
        $ingredientsString = implode(',', $ingredients);
        $response = $this->client->request('GET', 'https://api.spoonacular.com/recipes/findByIngredients', [
            'query' => [
                'ingredients' => $ingredientsString,
                'apiKey' => $this->apiKey
            ]
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function getRecipeDetails(int $recipeId)
    {
        $response = $this->client->request('GET', "https://api.spoonacular.com/recipes/{$recipeId}/information", [
            'query' => [
                'apiKey' => $this->apiKey
            ]
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    // Récupère les informations nutritionnelles d'une recette
    public function getRecipeNutrition(int $recipeId)
    {
        $response = $this->client->request('GET', "https://api.spoonacular.com/recipes/{$recipeId}/nutritionWidget.json", [
            'query' => [
                'apiKey' => $this->apiKey
            ]
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}
