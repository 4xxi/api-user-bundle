<?php

declare(strict_types=1);

namespace Fourxxi\ApiUserBundle\Security\Guard;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

final class JsonLoginAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function supports(Request $request)
    {
        $validRequest = $request->isMethod(Request::METHOD_POST)
            && 'application/json' === $request->headers->get('Content-Type')
            && !empty($request->getContent());
        ;

        $validJson = (bool) json_decode($request->getContent(), true);

        return $validRequest && $validJson;
    }

    public function getCredentials(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        return [
            'username' => isset($data['username']) ? $data['username'] : null,
            'password' => isset($data['password']) ? $data['password'] : null,
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        return $userProvider->loadUserByUsername($credentials['username']);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $response = new JsonResponse(['message' => 'Invalid username/password'], Response::HTTP_UNAUTHORIZED);
//        $event = new ApiAuthenticationRequestFailedEvent($request, $exception, $response);
//
//        $this->eventDispatcher->dispatch($event);
//
//        return $event->getResponse();
        return $response;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $response = new JsonResponse(['message' => 'Success']);
//        $event = new ApiAuthenticationRequestFailedEvent($request, $exception, $response);
//
//        $this->eventDispatcher->dispatch($event);
//
//        return $event->getResponse();
        return $response;
    }

    public function supportsRememberMe()
    {
        return false;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
//        $response = new JsonResponse(['message' => 'Authentication required'], Response::HTTP_UNAUTHORIZED);
//        $event = new ApiAuthenticationUnavailableEvent($request, $authException, $response);
//
//        $this->eventDispatcher->dispatch($event);
//
//        return $event->getResponse();

        return new JsonResponse(['invalid login data'], Response::HTTP_BAD_REQUEST);
    }
}