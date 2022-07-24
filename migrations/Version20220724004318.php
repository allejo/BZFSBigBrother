<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220724004318 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE IF NOT EXISTS joins (joinid INT UNSIGNED AUTO_INCREMENT NOT NULL, callsignid INT UNSIGNED DEFAULT NULL, addressid INT UNSIGNED DEFAULT NULL, eventtime DATETIME DEFAULT NULL, INDEX when_INDEX (eventtime), PRIMARY KEY(joinid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;');
        $this->addSql('CREATE TABLE IF NOT EXISTS rawlog (rawlogid INT UNSIGNED AUTO_INCREMENT NOT NULL, callsign VARCHAR(32) DEFAULT NULL, bzid VARCHAR(32) DEFAULT NULL, ipaddress VARCHAR(15) DEFAULT NULL, hostname VARCHAR(255) DEFAULT NULL, apikey VARCHAR(40) NOT NULL, eventtime DATETIME DEFAULT NULL, build VARCHAR(255) DEFAULT NULL, INDEX apikey (apikey), PRIMARY KEY(rawlogid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;');
        $this->addSql('CREATE TABLE IF NOT EXISTS apikeys (apikeyid INT UNSIGNED AUTO_INCREMENT NOT NULL, apikey VARCHAR(40) NOT NULL, active TINYINT(1) NOT NULL, owner INT UNSIGNED NOT NULL, INDEX active (active), INDEX owner (owner), UNIQUE INDEX `key` (apikey), PRIMARY KEY(apikeyid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;');
        $this->addSql('CREATE TABLE IF NOT EXISTS addresses (addressid INT UNSIGNED AUTO_INCREMENT NOT NULL, ipaddress VARCHAR(15) DEFAULT NULL, hostname VARCHAR(255) DEFAULT NULL, INDEX hostname_INDEX (hostname), UNIQUE INDEX iphost_INDEX (ipaddress, hostname), PRIMARY KEY(addressid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;');
        $this->addSql('CREATE TABLE IF NOT EXISTS callsigns (callsignid INT UNSIGNED AUTO_INCREMENT NOT NULL, callsign VARCHAR(32) DEFAULT NULL, UNIQUE INDEX callsign_UNIQUE (callsign), PRIMARY KEY(callsignid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS joins');
        $this->addSql('DROP TABLE IF EXISTS rawlog');
        $this->addSql('DROP TABLE IF EXISTS apikeys');
        $this->addSql('DROP TABLE IF EXISTS addresses');
        $this->addSql('DROP TABLE IF EXISTS callsigns');
    }
}
