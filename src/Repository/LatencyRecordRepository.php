<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\LatencyRecord;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LatencyRecord>
 *
 * @method null|LatencyRecord find($id, $lockMode = null, $lockVersion = null)
 * @method null|LatencyRecord findOneBy(array $criteria, array $orderBy = null)
 * @method LatencyRecord[]    findAll()
 * @method LatencyRecord[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LatencyRecordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LatencyRecord::class);
    }

    public function add(LatencyRecord $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(LatencyRecord $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
