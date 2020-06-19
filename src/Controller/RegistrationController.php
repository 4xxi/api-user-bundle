<?php

declare(strict_types=1);

namespace Fourxxi\ApiUserBundle\Controller;

use Fourxxi\ApiUserBundle\Event\Controller\RegistrationFormValidationFailedEvent;
use Fourxxi\ApiUserBundle\Event\Controller\RegistrationResponseCompletedEvent;
use Fourxxi\ApiUserBundle\Event\Controller\RegistrationUserCompletedEvent;
use Fourxxi\ApiUserBundle\Event\Controller\RegistrationUserPrePersistEvent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

final class RegistrationController extends AbstractController
{
    /**
     * @var AbstractType
     */
    private $registrationFormType;

    /**
     * @var string
     */
    private $userClass;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(
        AbstractType $registrationFormType,
        string $userClass,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->registrationFormType = $registrationFormType;
        $this->userClass = $userClass;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return JsonResponse|Response|null
     */
    public function register(Request $request)
    {
        $form = $this->createForm(get_class($this->registrationFormType), null, ['data_class' => $this->userClass]);
        $form->submit($this->getJsonPayloadFromRequest($request));

        if (!$form->isValid()) {
            $response = new JsonResponse($this->getErrors($form), Response::HTTP_BAD_REQUEST);
            $validationFailedEvent = new RegistrationFormValidationFailedEvent($form, $response);
            $this->eventDispatcher->dispatch($validationFailedEvent);

            return $validationFailedEvent->getResponse();
        }

        /** @var UserInterface $user */
        $user = $form->getData();
        $em = $this->getDoctrine()->getManager();

        $prePersistEvent = new RegistrationUserPrePersistEvent($user);
        $this->eventDispatcher->dispatch($prePersistEvent);

        $em->persist($user);
        $em->flush();

        $completedEvent = new RegistrationUserCompletedEvent($user);
        $this->eventDispatcher->dispatch($completedEvent);

        $responseEvent = new RegistrationResponseCompletedEvent($user);
        $this->eventDispatcher->dispatch($responseEvent);

        if (null === $responseEvent->getResponse()) {
            return new Response(null, Response::HTTP_NO_CONTENT);
        }

        return $responseEvent->getResponse();
    }

    private function getJsonPayloadFromRequest(Request $request): array
    {
        $content = $request->getContent();
        if (null === $content || empty($content)) {
            return [];
        }

        $decoded = json_decode($content, true);
        if (!is_array($decoded)) {
            return [];
        }

        return $decoded;
    }

    private function getErrors(FormInterface $form): array
    {
        $data = [];
        foreach ($form->getErrors(true) as $error) {
            $data[$error->getOrigin()->getName()][] = $error->getMessage();
        }

        return $data;
    }
}
