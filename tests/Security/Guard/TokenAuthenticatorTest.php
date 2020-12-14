<?php

namespace Fourxxi\ApiUserBundle\Tests\Security\Guard;

use Fourxxi\ApiUserBundle\Model\Token;
use Fourxxi\ApiUserBundle\Provider\Security\Guard\CredentialsProviderInterface;
use Fourxxi\ApiUserBundle\Provider\TokenProviderInterface;
use Fourxxi\ApiUserBundle\Security\Guard\TokenAuthenticator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class TokenAuthenticatorTest extends TestCase
{
    private const EXPECTED_TOKEN_NAME = 'EXPECTED_TOKEN_NAME';
    /** @var CredentialsProviderInterface|MockObject */
    private $credentialsProvider;
    /** @var TokenProviderInterface|MockObject */
    private $tokenProvider;
    /** @var EventDispatcherInterface|MockObject */
    private $eventDispatcher;

    /** @var TokenAuthenticator */
    private $tokenAuthenticator;

    protected function setUp(): void
    {
        $this->credentialsProvider = $this->createMock(CredentialsProviderInterface::class);
        $this->tokenProvider = $this->createMock(TokenProviderInterface::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $this->tokenAuthenticator = new TokenAuthenticator(
            self::EXPECTED_TOKEN_NAME,
            $this->credentialsProvider,
            $this->tokenProvider,
            $this->eventDispatcher
        );
    }

    /**
     * @test
     */
    public function skipsEmptyToken(): void
    {
        $this
            ->credentialsProvider
            ->method('isValid')
            ->willReturn(true);

        $this->assertFalse($this->tokenAuthenticator->supports($this->requestWith()));
        $this->assertFalse($this->tokenAuthenticator->supports($this->requestWith([self::EXPECTED_TOKEN_NAME => ''])));
        $this->assertTrue($this->tokenAuthenticator->supports($this->requestWith([self::EXPECTED_TOKEN_NAME => 'valid'])));
    }

    /**
     * @test
     */
    public function skipsInvalidToken(): void
    {
        $this
            ->credentialsProvider
            ->method('isValid')
            ->willReturn(false);

        $this->assertFalse($this->tokenAuthenticator->supports($this->requestWith([self::EXPECTED_TOKEN_NAME => 'valid'])));
    }

    /**
     * @test
     */
    public function findsUserViaTokenProvider(): void
    {
        $mockUserProvider = $this->createMock(UserProviderInterface::class);
        $credentials = 'credentials';
        $expectedUser = new User('test', 'test');

        $this
            ->tokenProvider
            ->method('findTokenByCredentials')
            ->with($credentials)
            ->willReturn(null, new Token($expectedUser, new \DateTimeImmutable(), ''));

        $this->assertNull($this->tokenAuthenticator->getUser($credentials, $mockUserProvider));
        $this->assertSame($expectedUser, $this->tokenAuthenticator->getUser($credentials, $mockUserProvider));
    }

    /**
     * @test
     */
    public function getsTokenFromRequestHeader(): void
    {
        $request = $this->requestWith([self::EXPECTED_TOKEN_NAME => 'expected_header']);
        $this->credentialsProvider->method('getCredentials')
            ->with('expected_header')
            ->willReturn('expected_credentials');

        $this->assertEquals('expected_credentials', $this->tokenAuthenticator->getCredentials($request));
    }

    private function requestWith(array $headers = []): Request
    {
        $request = new Request();
        $request->headers->add($headers);

        return $request;
    }
}
