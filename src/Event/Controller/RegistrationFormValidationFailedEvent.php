<?php

declare(strict_types=1);

namespace Fourxxi\ApiUserBundle\Event\Controller;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;

final class RegistrationFormValidationFailedEvent extends Event
{
    /**
     * @var FormInterface
     */
    private $form;

    /**
     * @var Response|null
     */
    private $response;

    public function __construct(FormInterface $form, Response $response = null)
    {
        $this->form = $form;
        $this->response = $response;
    }

    public function getForm(): FormInterface
    {
        return $this->form;
    }

    public function getResponse(): ?Response
    {
        return $this->response;
    }

    public function setResponse(?Response $response): void
    {
        $this->response = $response;
    }
}
