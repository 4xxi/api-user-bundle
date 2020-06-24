<?php

declare(strict_types=1);

namespace Fourxxi\ApiUserBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Fourxxi\ApiUserBundle\Model\Token;
use Fourxxi\ApiUserBundle\Model\TokenInterface;
use Fourxxi\ApiUserBundle\Provider\TokenProviderInterface;
use Fourxxi\ApiUserBundle\Service\TokenCredentialsGeneratorInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class TokenRepository extends ServiceEntityRepository implements TokenProviderInterface
{
    /**
     * @var int
     */
    private $tokenLifetime;

    /**
     * @var TokenCredentialsGeneratorInterface
     */
    private $credentialsGenerator;

    public function __construct(
        ManagerRegistry $registry,
        int $tokenLifetime,
        TokenCredentialsGeneratorInterface $credentialsGenerator
    ) {
        parent::__construct($registry, Token::class);
        $this->tokenLifetime = $tokenLifetime;
        $this->credentialsGenerator = $credentialsGenerator;
    }

    public function findTokenByCredentials(string $credentials): ?TokenInterface
    {
        $qb = $this->createQueryBuilder('token');
        $qb
            ->andWhere('token.expiresAt > :now')
            ->andWhere('token.credentials = :credentials')
            ->setParameter('now', new \DateTimeImmutable())
            ->setParameter('credentials', $credentials)
            ->setMaxResults(1)
        ;

        /** @var TokenInterface $result */
        $result = $qb->getQuery()->getOneOrNullResult();
        if (null === $result) {
            return null;
        }

        return $result;
    }

    public function createTokenForUser(UserInterface $user): TokenInterface
    {
        $expiresAt = new \DateTimeImmutable('+'.$this->tokenLifetime.' seconds');
        $credentials = $this->credentialsGenerator->generate();
        $token = new Token($user, $expiresAt, $credentials);

        $this->getEntityManager()->persist($token);
        $this->getEntityManager()->flush();

        return $token;
    }
}
