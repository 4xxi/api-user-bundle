<?php

declare(strict_types=1);

namespace Fourxxi\ApiUserBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Fourxxi\ApiUserBundle\Entity\Token;
use Fourxxi\ApiUserBundle\Provider\ApiUserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class TokenRepository extends ServiceEntityRepository implements ApiUserProviderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Token::class);
    }

    public function findUserByTokenCredentials(string $credentials): ?UserInterface
    {
        $qb = $this->createQueryBuilder('token');
        $qb
            ->andWhere('token.expiresAt > :now')
            ->andWhere('token.credentials = :credentials')
            ->setParameter('now', new \DateTimeImmutable())
            ->setParameter('credentials', $credentials)
            ->setMaxResults(1)
        ;

        /** @var Token $result */
        $result = $qb->getQuery()->getOneOrNullResult();
        if (null === $result) {
            return null;
        }

        return $result->getUser();
    }
}