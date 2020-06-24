<?php

declare(strict_types=1);

namespace Fourxxi\ApiUserBundle\EventSubscriber;

use Fourxxi\ApiUserBundle\Event\Controller\RegistrationUserPrePersistEvent;
use Fourxxi\ApiUserBundle\Model\ConfirmableUserInterface;
use Fourxxi\ApiUserBundle\Service\TokenCredentialsGeneratorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class UserConfirmationSubscriber implements EventSubscriberInterface
{
    /**
     * @var TokenCredentialsGeneratorInterface
     */
    private $credentialsGenerator;

    public static function getSubscribedEvents()
    {
        return [
            RegistrationUserPrePersistEvent::class => ['onUserPrePersist'],
        ];
    }

    public function __construct(TokenCredentialsGeneratorInterface $credentialsGenerator)
    {
        $this->credentialsGenerator = $credentialsGenerator;
    }

    public function onUserPrePersist(RegistrationUserPrePersistEvent $event): void
    {
        if (!is_subclass_of($event->getUser(), ConfirmableUserInterface::class)) {
            return;
        }

        /** @var ConfirmableUserInterface $user */
        $user = $event->getUser();
        $user->setConfirmationToken($this->credentialsGenerator->generate());

        // todo: send emails
    }
}
