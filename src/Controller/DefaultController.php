<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/')]
class DefaultController extends AbstractController
{
    #[Route('')]
    #[Cache(expires: 'tomorrow midnight', maxage: 3600, public: true)]
    public function index(): Response
    {
        return $this->render('default/index.html.twig');
    }
}
