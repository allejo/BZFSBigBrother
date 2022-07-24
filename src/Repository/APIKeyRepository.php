<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\APIKey;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<APIKey>
 *
 * @method null|APIKey find($id, $lockMode = null, $lockVersion = null)
 * @method null|APIKey findOneBy(array $criteria, array $orderBy = null)
 * @method APIKey[]    findAll()
 * @method APIKey[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class APIKeyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, APIKey::class);
    }

    public function add(APIKey $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(APIKey $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
