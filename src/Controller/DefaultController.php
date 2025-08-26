<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/')]
class DefaultController extends AbstractController
{
    #[Route('')]
    public function index(): Response
    {
        $response = $this->render('default/index.html.twig');

        // La gestion du cache HTTP se fait directement sur la réponse
        // via les en-têtes "cache-control", "expires", "etag", etc.
        // En environnement de dev, Symfony force à avoir un rafraîchissement systématique
        $response->headers->set('cache-control', 'max-age=0, public');
        // Il existe des raccourcis pour manipuler spécifiquement ces en-têtes
        $response->headers->addCacheControlDirective('no-cache');
        $response->headers->addCacheControlDirective('public');
        $response->setPublic();
        $response->setMaxAge(3600);

        return $response;
    }
}
