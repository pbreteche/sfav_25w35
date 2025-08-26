<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

#[Route('/')]
class DefaultController extends AbstractController
{
    #[Route('')]
    #[Cache(expires: 'tomorrow midnight', maxage: 3600, public: true)]
    public function index(
        Request $request,
        CacheInterface $cache,
    ): Response {
        $response = new Response();
        // L'etag est une chaîne de caractères qui doit unique pour chaque version du document
        // On reste complètement libre de le calculer comme on veut.
        // On choisira une méthode à faible coût (md5, autre).
        $response->setEtag($request->getLocale());
        if ($response->isNotModified($request)) {
            // réponse sans corps avec statut "304 Not Modified"
            return $response;
        }

        // Ici, il n'y a pas d'anticipation afin d'écrire la donnée en prévision de pouvoir la lire ultérieurement.
        // On tente seulement de lire la donnée, et on fournit une fonction de régénération au cas où la donnée
        // n'existe pas, ou elle existe, mais est périmée.
        $cachedData = $cache->get('notre_cle_unique', function (ItemInterface $item) {
            $item->expiresAfter(3600);

            // Calcul couteux
            usleep(500000);

            return 'la donnée produite par un calcul couteux';
        });

        return $this->render('default/index.html.twig', [
            'data' => $cachedData,
        ]);
    }
}
