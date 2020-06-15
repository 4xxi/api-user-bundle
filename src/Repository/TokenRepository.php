<?php

declare(strict_types=1);

namespace Fourxxi\ApiUserBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Fourxxi\ApiUserBundle\Entity\Token;
use Fourxxi\ApiUserBundle\Entity\TokenInterface;
use Fourxxi\ApiUserBundle\Provider\ApiTokenProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class TokenRepository extends ServiceEntityRepository implements ApiTokenProviderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Token::class);
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
}