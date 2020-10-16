<?php

namespace Fourxxi\ApiUserBundle\Tests\Unit\Security\Guard;

use Fourxxi\ApiUserBundle\Event\Security\Guard\LoginAuthenticationFailedEvent;
use Fourxxi\ApiUserBundle\Event\Security\Guard\LoginAuthenticationSuccessEvent;
use Fourxxi\ApiUserBundle\Model\Token;
use Fourxxi\ApiUserBundle\Provider\TokenProviderInterface;
use Fourxxi\ApiUserBundle\Security\Exception\UserNotConfirmedException;
use Fourxxi\ApiUserBundle\Security\Guard\JsonLoginAuthenticator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class JsonLoginAuthenticatorTest extends TestCase
{
    /** @var TokenProviderInterface */
    private $tokenProvider;
    /** @var EventDispatcherInterface */
    private $eventDispatcher;
    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    /** @var JsonLoginAuthenticator */
    private $jsonLoginAuthenticator;

    protected function setUp(): void
    {
        $this->tokenProvider = $this->createMock(TokenProviderInterface::class);
        $this->passwordEncoder = $this->createMock(UserPasswordEncoderInterface::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $this->jsonLoginAuthenticator = new JsonLoginAuthenticator(
            $this->tokenProvider,
            $this->passwordEncoder,
            $this->eventDispatcher
        );
    }


    public function authRequestsDataProvider(): array
    {
        return [
            ['{"username": "expected_username", "password": "expected_password"}', 'expected_username', 'expected_password'],
            ['{"username": "expected_username"}', 'expected_username', null],
            ['{"password": "expected_password"}', null, 'expected_password'],
            ['unexpected string', null, null],
        ];
    }

    /**
     * @test
     */
    public function onlySupportsLoginRoute(): void
    {
        $request = new Request();
        $request->attributes->set('_route', 'api_user_login');

        $this->assertTrue($this->jsonLoginAuthenticator->supports($request));

        $request->attributes->set('_route', 'wrong_route');

        $this->assertFalse($this->jsonLoginAuthenticator->supports($request));
    }

    /**
     * @test
     * @dataProvider authRequestsDataProvider
     *
     * @param string $requestContent
     * @param string|null $expectedUsername
     * @param string|null $expectedPassword
     */
    public function parsesRequestToGetCredentials(
        string $requestContent,
        ?string $expectedUsername,
        ?string $expectedPassword
    ): void
    {
        $request = new Request([], [], [], [], [], [], $requestContent);

        $credentials = $this->jsonLoginAuthenticator->getCredentials($request);

        $this->assertSame($expectedUsername, $credentials['username']);
        $this->assertSame($expectedPassword, $credentials['password']);
    }

    /**
     * @test
     */
    public function userIsLoadedFromUserProvider(): void
    {
        $expectedUser = new User('test', 'test');
        $userProvider = $this->createMock(UserProviderInterface::class);
        $userProvider
            ->expects($this->once())
            ->method('loadUserByUsername')
            ->with('expected_username')
            ->willReturn($expectedUser);

        $actualUser = $this->jsonLoginAuthenticator->getUser(['username' => 'expected_username'], $userProvider);

        $this->assertSame($expectedUser, $actualUser);
    }

    /**
     * @test
     */
    public function credentialsAreCheckedWithPasswordEncoder(): void
    {
        $expectedUser = new User('test', 'test');
        $this->passwordEncoder
            ->expects($this->once())
            ->method('isPasswordValid')
            ->with($expectedUser, 'expected_password')
            ->willReturn(true);

        $this->assertTrue(
            $this->jsonLoginAuthenticator->checkCredentials(['password' => 'expected_password'], $expectedUser)
        );
    }

    /**
     * @test
     */
    public function returnsUnauthorizedOnAuthenticationException(): void
    {
        $response = $this->jsonLoginAuthenticator->onAuthenticationFailure(new Request(), new AuthenticationException());

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public static function authenticationFailures(): array
    {
        return [
            [new AuthenticationException(), LoginAuthenticationFailedEvent::class],
            [new UserNotConfirmedException(), LoginAuthenticationFailedEvent::class],
        ];
    }

    /**
     * @dataProvider authenticationFailures
     * @test
     * @param AuthenticationException $exception
     * @param string $expectedEvent
     */
    public function hasCustomEventsForAuthenticationFailures(AuthenticationException $exception, string $expectedEvent): void
    {
        $this
            ->eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf($expectedEvent));

        $this->jsonLoginAuthenticator->onAuthenticationFailure(new Request(), $exception);
    }

    /**
     * @test
     */
    public function returnsTokenOnSuccessfulAuthentication(): void
    {
        $expectedUser = new User('test', 'test');
        $expectedExpiresAt = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2020-01-01 01:00:00');

        $symfonyToken = new UsernamePasswordToken($expectedUser, [], 'test');
        $tokenDTO = new Token(
            $expectedUser,
            $expectedExpiresAt,
            'expected_api_token'
        );

        $this
            ->tokenProvider
            ->expects($this->once())
            ->method('createTokenForUser')
            ->willReturn($tokenDTO);
        $response = $this->jsonLoginAuthenticator->onAuthenticationSuccess(new Request(), $symfonyToken, 'test');

        $this->assertTrue($response->isSuccessful());
        $this->assertStringContainsString('expected_api_token', $response->getContent());
        $this->assertStringContainsString($expectedExpiresAt->format(DATE_ATOM), $response->getContent());
    }

    /**
     * @test
     */
    public function hasCustomEventForAuthenticationSuccess(): void
    {
        $expectedUser = new User('test', 'test');
        $symfonyToken = new UsernamePasswordToken($expectedUser, [], 'test');

        $this
            ->eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(LoginAuthenticationSuccessEvent::class));

        $this->jsonLoginAuthenticator->onAuthenticationSuccess(new Request(), $symfonyToken, 'test');
    }
}
