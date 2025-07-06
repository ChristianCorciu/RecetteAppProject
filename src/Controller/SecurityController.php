<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp\Client;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route(path: '/login', name: 'app_login', methods: ['GET', 'POST'])]
public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
{
    if ($request->isMethod('POST')) {
        $email = $request->request->get('email');
        $password = $request->request->get('password');

        $client = new Client();

        try {
            $response = $client->post('https://pjrvjsjzoucvboyluonr.supabase.co/auth/v1/token', [
                'headers' => [
                    'apikey' => 'VOTRE_SUPABASE_API_KEY',
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'email' => $email,
                    'password' => $password,
                ],
            ]);
            
            $data = json_decode($response->getBody(), true);

            // Vérifier si token reçu
            if (isset($data['access_token'])) {
                // Ici, créer la session Symfony (manuellement ou via un custom authenticator)
                // Exemple simple : stocker dans la session Symfony
                $this->get('session')->set('user_token', $data['access_token']);
                
                return $this->redirectToRoute('homepage'); // Ou autre page
            } else {
                $error = 'Échec de la connexion';
            }
        } catch (\Exception $e) {
            $error = 'Erreur lors de la connexion : ' . $e->getMessage();
        }
    } else {
        $error = $authenticationUtils->getLastAuthenticationError();
    }

    return $this->render('security/login.html.twig', [
        'error' => $error ?? null,
    ]);
}
