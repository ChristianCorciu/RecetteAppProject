<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class RecipeControllerTest extends WebTestCase
{
    public function testCreateRecipeAsAuthenticatedUser(): void
    {
        $client = static::createClient();
        $container = static::getContainer();

        // Récupère un utilisateur existant (à adapter selon tes fixtures ou base test)
        /** @var User $user */
        $user = $container->get('doctrine')->getRepository(User::class)->findOneByEmail('user@test.com');
        $client->loginUser($user);

        // Accès au formulaire de création
        $crawler = $client->request('GET', '/recette/ajouter');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');

        // Remplissage du formulaire
        $form = $crawler->selectButton('Ajouter')->form([
            'recipe[name]' => 'Soupe test',
            'recipe[description]' => 'Délicieuse soupe automatisée.',
            'recipe[category]' => 1, // À adapter : ID d'une catégorie existante
        ]);

        $client->submit($form);

        // Vérifie la redirection après soumission
        $this->assertResponseRedirects('/home');
        $client->followRedirect();

        // Vérifie que la recette s'affiche bien
        $this->assertSelectorTextContains('.card-title', 'Soupe test');
    }

    public function testCreateRecipeAsAnonymousUser(): void
    {
        $client = static::createClient();

        // Accès au formulaire de création sans être connecté
        $client->request('GET', '/recette/ajouter');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND); // Redirection vers la page de connexion

        // Vérifie que l'utilisateur est redirigé vers la page de connexion
        $this->assertResponseRedirects('/login');
    }
}