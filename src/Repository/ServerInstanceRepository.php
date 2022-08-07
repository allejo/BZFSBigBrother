<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\ServerInstance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ServerInstance>
 *
 * @method null|ServerInstance find($id, $lockMode = null, $lockVersion = null)
 * @method null|ServerInstance findOneBy(array $criteria, array $orderBy = null)
 * @method ServerInstance[]    findAll()
 * @method ServerInstance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServerInstanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServerInstance::class);
    }

    public function add(ServerInstance $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ServerInstance $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
