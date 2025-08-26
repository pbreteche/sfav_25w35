<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/')]
class DefaultController extends AbstractController
{
    #[Route('')]
    #[Cache(expires: 'tomorrow midnight', maxage: 3600, public: true)]
    public function index(Request $request): Response
    {
        $response = new Response();
        // L'etag est une chaîne de caractères qui doit unique pour chaque version du document
        // On reste complètement libre de le calculer comme on veut.
        // On choisira une méthode à faible coût (md5, autre).
        $response->setEtag($request->getLocale());
        if ($response->isNotModified($request)) {
            // réponse sans corps avec statut "304 Not Modified"
            return $response;
        }

        return $this->render('default/index.html.twig');
    }
}
