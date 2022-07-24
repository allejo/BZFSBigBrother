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
}
