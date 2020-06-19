<?php

declare(strict_types=1);

namespace Fourxxi\ApiUserBundle\EventSubscriber;

use Fourxxi\ApiUserBundle\Event\Controller\RegistrationUserPrePersistEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class EncodeUserPasswordSubscriber implements EventSubscriberInterface
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public static function getSubscribedEvents()
    {
        return [
            RegistrationUserPrePersistEvent::class => ['onUserPrePersist'],
        ];
    }

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function onUserPrePersist(RegistrationUserPrePersistEvent $event): void
    {
        $user = $event->getUser();
        $encoded = $this->passwordEncoder->encodePassword($user, $user->getPassword());
        $user->setPassword($encoded);
    }
}
