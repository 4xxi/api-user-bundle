<?php

declare(strict_types=1);

namespace Fourxxi\ApiUserBundle\Security\Guard;

use Fourxxi\ApiUserBundle\Provider\ApiUserProviderInterface;
use Fourxxi\ApiUserBundle\Provider\Security\Guard\CredentialsProviderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

final class ApiAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var string
     */
    private $tokenName;

    /**
     * @var bool
     */
    private $checkQueryString;

    /**
     * @var CredentialsProviderInterface
     */
    private $credentialsProvider;

    /**
     * @var ApiUserProviderInterface
     */
    private $userProvider;

    public function __construct(
        string $tokenName,
        bool $checkQueryString,
        CredentialsProviderInterface $credentialsProvider,
        ApiUserProviderInterface $userProvider
    ) {
        $this->tokenName = $tokenName;
        $this->checkQueryString = $checkQueryString;
        $this->credentialsProvider = $credentialsProvider;
        $this->userProvider = $userProvider;
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
        return $this->userProvider->findUserByTokenCredentials($credentials);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = [
            // you might translate this message
            'message' => 'Authentication Failed'
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
    }

    public function supportsRememberMe()
    {
        return false;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = [
            // you might translate this message
            'message' => 'Authentication Required'
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    private function getTokenFromRequest(Request $request): ?string
    {
        if ($request->headers->has($this->tokenName)) {
            return trim($request->headers->get($this->tokenName));
        } elseif ($this->checkQueryString && $request->query->has($this->tokenName)) {
            return trim($request->query->get($this->tokenName));
        }

        return null;
    }
}