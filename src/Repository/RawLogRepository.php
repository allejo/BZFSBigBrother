<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\RawLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RawLog>
 *
 * @method null|RawLog find($id, $lockMode = null, $lockVersion = null)
 * @method null|RawLog findOneBy(array $criteria, array $orderBy = null)
 * @method RawLog[]    findAll()
 * @method RawLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RawLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RawLog::class);
    }

    public function add(RawLog $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(RawLog $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
