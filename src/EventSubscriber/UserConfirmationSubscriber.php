<?php

declare(strict_types=1);

namespace Fourxxi\ApiUserBundle\EventSubscriber;

use Fourxxi\ApiUserBundle\Event\Controller\RegistrationUserPrePersistEvent;
use Fourxxi\ApiUserBundle\Model\ConfirmableUserInterface;
use Fourxxi\ApiUserBundle\Sender\MessageSenderInterface;
use Fourxxi\ApiUserBundle\Service\FrontendRouterInterface;
use Fourxxi\ApiUserBundle\Service\TokenCredentialsGeneratorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class UserConfirmationSubscriber implements EventSubscriberInterface
{
    /**
     * @var TokenCredentialsGeneratorInterface
     */
    private $credentialsGenerator;

    /**
     * @var MessageSenderInterface
     */
    private $messageSender;

    /**
     * @var FrontendRouterInterface
     */
    private $router;

    public static function getSubscribedEvents()
    {
        return [
            RegistrationUserPrePersistEvent::class => ['onUserPrePersist'],
        ];
    }

    public function __construct(
        TokenCredentialsGeneratorInterface $credentialsGenerator,
        MessageSenderInterface $messageSender,
        FrontendRouterInterface $router
    ) {
        $this->credentialsGenerator = $credentialsGenerator;
        $this->messageSender = $messageSender;
        $this->router = $router;
    }

    public function onUserPrePersist(RegistrationUserPrePersistEvent $event): void
    {
        $user = $event->getUser();

        if (!$user instanceof ConfirmableUserInterface) {
            return;
        }

        $token = $this->credentialsGenerator->generate();
        $user->setConfirmationToken($token);
        $this->messageSender->sendConfirmationMessage($user, $this->router->getConfirmationRoute($token));
    }
}
