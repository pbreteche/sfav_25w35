<?php

declare(strict_types=1);

namespace App\Controller;

use App\Constants;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/locale')]
class LocaleController extends AbstractController
{
    #[Route('', methods: 'POST')]
    public function set(
        Request $request,
        ValidatorInterface $validator,
    ): Response {
        $locale = $request->request->get('locale');
        $violations = $validator->validate($locale, [
            new NotBlank(),
            new Choice(choices: Constants::MANAGED_LOCALE),
        ]);

        if (0 === $violations->count()) {
            $request->getSession()->set('locale', $locale);

            return $this->redirectToRoute('app_default_index');
        }

        throw new UnprocessableEntityHttpException(
            sprintf('Locale should be in (%s)', implode(', ', Constants::MANAGED_LOCALE))
        );
    }
}
