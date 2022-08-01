<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\PlayerJoin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PlayerJoin>
 *
 * @method null|PlayerJoin find($id, $lockMode = null, $lockVersion = null)
 * @method null|PlayerJoin findOneBy(array $criteria, array $orderBy = null)
 * @method PlayerJoin[]    findAll()
 * @method PlayerJoin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlayerJoinRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlayerJoin::class);
    }

    public function add(PlayerJoin $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PlayerJoin $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findUniqueJoinsByIP(string $ipAddress, int $daysBack = 180): array
    {
        return $this->createQueryBuilder('j')
            ->select('c.callsign', 'a.ipAddress', 'a.hostname', 'COUNT(j.id) times')
            ->distinct()
            ->join('j.address', 'a')
            ->join('j.callsign', 'c')
            ->where('a.ipAddress = :ipAddress')
            ->andWhere("j.eventTime > DATE_SUB(CURRENT_TIMESTAMP(), :daysBack, 'DAY')")
            ->groupBy('c.callsign')
            ->addGroupBy('a.ipAddress')
            ->addGroupBy('a.hostname')
            ->setParameter('ipAddress', $ipAddress)
            ->setParameter('daysBack', $daysBack)
            ->getQuery()
            ->getArrayResult()
        ;
    }

    public function findUniqueIPsByCallsign(string $callsign, int $daysBack = 180): array
    {
        return $this->createQueryBuilder('j')
            ->select('a.ip_address')
            ->distinct()
            ->join('j.address', 'a')
            ->join('j.callsign', 'c')
            ->where('c.callsign = :callsign')
            ->andWhere("j.eventTime > DATE_SUB(CURRENT_TIMESTAMP(), :daysBack, 'DAY')")
            ->setParameter('callsign', $callsign)
            ->setParameter('daysBack', $daysBack)
            ->getQuery()
            ->getArrayResult()
        ;
    }
}
