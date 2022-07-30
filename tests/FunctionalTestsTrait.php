<?php

namespace App\Tests;

use Doctrine\DBAL\Exception as DoctrineException;
use Doctrine\ORM\EntityManagerInterface;

trait FunctionalTestsTrait
{
    protected EntityManagerInterface $em;

    protected function getEntityManager(): EntityManagerInterface
    {
        return $this->em;
    }

    /**
     * @param array<class-string> $entities
     *
     * @see https://symfonycasts.com/screencast/phpunit/control-database
     *
     * @throws DoctrineException
     */
    protected function truncateEntities(array $entities): void
    {
        $connection = $this->getEntityManager()->getConnection();
        $databasePlatform = $connection->getDatabasePlatform();

        if ($databasePlatform->supportsForeignKeyConstraints()) {
            $connection->executeQuery('SET FOREIGN_KEY_CHECKS=0');
        }

        foreach ($entities as $entity) {
            $query = $databasePlatform->getTruncateTableSQL(
                $this->getEntityManager()->getClassMetadata($entity)->getTableName()
            );
            $connection->executeQuery($query);
        }

        if ($databasePlatform->supportsForeignKeyConstraints()) {
            $connection->executeQuery('SET FOREIGN_KEY_CHECKS=1');
        }
    }
}
