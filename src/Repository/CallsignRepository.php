<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Callsign;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Callsign>
 *
 * @method null|Callsign find($id, $lockMode = null, $lockVersion = null)
 * @method null|Callsign findOneBy(array $criteria, array $orderBy = null)
 * @method Callsign[]    findAll()
 * @method Callsign[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CallsignRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Callsign::class);
    }

    public function add(Callsign $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Callsign $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
