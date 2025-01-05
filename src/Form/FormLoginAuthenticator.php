<?php
// src/Security/FormLoginAuthenticator.php
namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;
use Symfony\Component\Routing\RouterInterface;

class FormLoginAuthenticator extends AbstractGuardAuthenticator
{
    private $entityManager;
    private $passwordEncoder;
    private $router;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder, RouterInterface $router)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->router = $router;
    }

    public function supports(Request $request)
{
    // Vérifie si la requête contient un login
    return $request->attributes->get('_route') === 'login' && $request->isMethod('POST');
}


    public function getCredentials(Request $request)
    {
        // Récupère les informations du formulaire
        return [
            'email' => $request->request->get('email'),
            'password' => $request->request->get('password'),
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        // Cherche l'utilisateur par son email
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $credentials['email']]);

        // Si l'utilisateur n'existe pas, on retourne null
        if (!$user) {
            return null;
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        // Vérifie que le mot de passe est correct
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

  

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // Récupère l'utilisateur connecté
        $user = $token->getUser();
    
        // Redirige vers une page avec l'email et le nom de l'utilisateur dans l'URL
        return new RedirectResponse($this->router->generate('user_profile', [
            'email' => $user->getEmail(),
            'name' => $user->getName() // Assurez-vous que la méthode getFullName() existe dans votre entité User
        ]));
    }
    
    
    public function start(Request $request, AuthenticationException $authException = null)
    {
        // Démarre le processus d'authentification
        return new RedirectResponse($this->router->generate('login'));
    }

    public function supportsRememberMe()
    {
        return false;
    }
    
}
