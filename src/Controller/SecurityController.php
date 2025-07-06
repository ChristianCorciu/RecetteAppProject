<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp\Client;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
       if ($request->isMethod('POST')) {
        $postData = $request->request->all();
        $logger->info('Données POST reçues', $postData);
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

                if (isset($data['access_token'])) {
                    $this->get('session')->set('user_token', $data['access_token']);

                    return $this->redirectToRoute('homepage');
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
}
