<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController
{
    /** @var \Twig_Environment */
    private $twig;

    /**
     * @param \Twig_Environment $twig
     */
    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @Route("/login", name="security_login")
     *
     * @param AuthenticationUtils $utils
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function login(AuthenticationUtils $utils): Response
    {
        return new Response($this->twig->render(
            'security/login.html.twig',
            [
                'last_username' => $utils->getLastUsername(),
                'error' => $utils->getLastAuthenticationError(),
            ]
        ));
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout(): void
    {
        
    }
}