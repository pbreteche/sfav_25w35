<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Part\DataPart;
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

    #[Route('/mail')]
    public function mailSomething(
        MailerInterface $mailer,
    ): Response {
        $tos = [
            new Address('username@example.com', 'John Doe'),
            new Address('othername@example.com', 'Jane Doe'),
            'anameagain@example.com',
        ];

        $csvFile = fopen('php://temp', 'r+');
        fputcsv($csvFile, ['apple', 'green', 35]);
        fputcsv($csvFile, ['banana', 'yellow', 12]);
        fputcsv($csvFile, ['cherry', 'red', 85]);
        rewind($csvFile);
        $csvFileContent = stream_get_contents($csvFile);
        fclose($csvFile);

        $message = new TemplatedEmail()
            ->htmlTemplate('default/email/email.html.twig')
            ->context([
                'name' => 'Your name',
                'date' => 'tomorrow'
            ])
            ->to(...$tos)
            ->subject('Your subject')
            ->cc('copyto@ewample.com')
            ->bcc('blindcopy@example.com')
            ->from('noreply@example.com')
            ->attach($csvFileContent, 'fruits.csv', 'text/csv')
            ->addPart(new DataPart($csvFileContent, 'fruits.csv', 'text/csv'))
        ;

        $mailer->send($message);

        return $this->render('default/index.html.twig', [
            'data' => null,
        ]);
    }
}
