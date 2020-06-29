<?php

declare(strict_types=1);

namespace Fourxxi\ApiUserBundle\Controller;

use Fourxxi\ApiUserBundle\Model\ConfirmableUserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ConfirmationController extends AbstractController
{
    /**
     * @var string
     */
    private $userClass;

    public function __construct(string $userClass)
    {
        $this->userClass = $userClass;
    }

    /**
     * @return Response
     */
    public function __invoke(Request $request, ?string $token = null)
    {
        if (null === $token) {
            throw new BadRequestHttpException();
        }

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository($this->userClass)->findOneBy([
            'confirmationToken' => $token,
            'confirmed' => false,
        ]);

        if (null === $user || !$user instanceof ConfirmableUserInterface) {
            throw new NotFoundHttpException();
        }

        $user->confirm();
        $em->flush();

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
