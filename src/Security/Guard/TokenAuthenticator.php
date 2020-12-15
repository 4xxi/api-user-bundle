<?php

declare(strict_types=1);

namespace Fourxxi\ApiUserBundle\Security\Guard;

use Fourxxi\ApiUserBundle\Event\Security\Guard\TokenAuthenticationFailedEvent;
use Fourxxi\ApiUserBundle\Event\Security\Guard\TokenAuthenticationUnavailableEvent;
use Fourxxi\ApiUserBundle\Provider\Security\Guard\CredentialsProviderInterface;
use Fourxxi\ApiUserBundle\Provider\TokenProviderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class TokenAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var string
     */
    private $tokenName;

    /**
     * @var CredentialsProviderInterface
     */
    private $credentialsProvider;

    /**
     * @var TokenProviderInterface
     */
    private $tokenProvider;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(
        string $tokenName,
        CredentialsProviderInterface $credentialsProvider,
        TokenProviderInterface $tokenProvider,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->tokenName = $tokenName;
        $this->credentialsProvider = $credentialsProvider;
        $this->tokenProvider = $tokenProvider;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function supports(Request $request)
    {
        $token = $this->getTokenFromRequest($request);

        return null !== $token && !empty($token) && $this->credentialsProvider->isValid($token);
    }

    public function getCredentials(Request $request)
    {
        $token = $this->getTokenFromRequest($request);

        if (null !== $token) {
            $token = $this->credentialsProvider->getCredentials($token);
        }

        return $token;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = $this->tokenProvider->findTokenByCredentials($credentials);
        if (null === $token) {
            return null;
        }

        return $token->getUser();
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $response = new JsonResponse(['message' => 'Authentication failed'], Response::HTTP_UNAUTHORIZED);
        $event = new TokenAuthenticationFailedEvent($request, $exception, $response);

        $this->eventDispatcher->dispatch($event);

        return $event->getResponse();
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
    }

    public function supportsRememberMe()
    {
        return false;
    }

    public function start(Request $request, AuthenticationException $authException = null): ?Response
    {
        $response = new JsonResponse(['message' => 'Authentication required'], Response::HTTP_UNAUTHORIZED);
        $event = new TokenAuthenticationUnavailableEvent($request, $authException, $response);
        $this->eventDispatcher->dispatch($event);

        return $event->getResponse();
    }

    private function getTokenFromRequest(Request $request): ?string
    {
        if ($request->headers->has($this->tokenName)) {
            return trim($request->headers->get($this->tokenName));
        }

        return null;
    }
}
