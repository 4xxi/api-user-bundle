<?php

declare(strict_types=1);

namespace Fourxxi\ApiUserBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Fourxxi\ApiUserBundle\Entity\ConfirmationToken;
use Fourxxi\ApiUserBundle\Entity\ConfirmationTokenInterface;
use Fourxxi\ApiUserBundle\Entity\Token;
use Fourxxi\ApiUserBundle\Entity\TokenInterface;
use Fourxxi\ApiUserBundle\Provider\ConfirmationTokenProviderInterface;
use Fourxxi\ApiUserBundle\Provider\TokenProviderInterface;
use Fourxxi\ApiUserBundle\Service\TokenCredentialsGeneratorInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ConfirmationTokenRepository extends ServiceEntityRepository implements ConfirmationTokenProviderInterface
{
    /**
     * @var TokenCredentialsGeneratorInterface
     */
    private $credentialsGenerator;

    public function __construct(
        ManagerRegistry $registry,
        TokenCredentialsGeneratorInterface $credentialsGenerator
    ) {
        parent::__construct($registry, ConfirmationToken::class);
        $this->credentialsGenerator = $credentialsGenerator;
    }

    public function findTokenByCredentials(string $credentials): ?ConfirmationTokenInterface
    {
        $qb = $this->createQueryBuilder('token');
        $qb
            ->andWhere('token.credentials = :credentials')
            ->setParameter('credentials', $credentials)
            ->setMaxResults(1)
        ;

        /** @var ConfirmationTokenInterface $result */
        $result = $qb->getQuery()->getOneOrNullResult();
        if (null === $result) {
            return null;
        }

        return $result;
    }

    public function createTokenForUser(UserInterface $user): ConfirmationTokenInterface
    {
        $credentials = $this->credentialsGenerator->generate();
        $token = new ConfirmationToken($user, $credentials);

        $this->getEntityManager()->persist($token);
        $this->getEntityManager()->flush();

        return $token;
    }
}
