<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220724064538 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE addresses CHANGE ipaddress ip_address VARCHAR(15) DEFAULT NULL');
        $this->addSql('ALTER TABLE apikeys CHANGE apikey `key` VARCHAR(40) NOT NULL');
        $this->addSql('ALTER TABLE joins CHANGE callsignid callsign_id INT(10) unsigned NOT NULL');
        $this->addSql('ALTER TABLE joins CHANGE addressid address_id INT(10) unsigned NOT NULL');
        $this->addSql('ALTER TABLE joins CHANGE eventtime event_time DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE rawlog CHANGE ipaddress ip_address VARCHAR(15) DEFAULT NULL');
        $this->addSql('ALTER TABLE rawlog CHANGE apikey apikey_id INT(10) unsigned NOT NULL');
        $this->addSql('ALTER TABLE rawlog CHANGE eventtime event_time DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE addresses CHANGE ip_address ipaddress VARCHAR(15) DEFAULT NULL');
        $this->addSql('ALTER TABLE apikeys CHANGE `key` apikey VARCHAR(40) NOT NULL');
        $this->addSql('ALTER TABLE joins CHANGE callsign_id callsignid INT(10) unsigned NOT NULL');
        $this->addSql('ALTER TABLE joins CHANGE address_id addressid INT(10) unsigned NOT NULL');
        $this->addSql('ALTER TABLE joins CHANGE event_time eventtime DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE rawlog CHANGE ip_address ipaddress VARCHAR(15) DEFAULT NULL');
        $this->addSql('ALTER TABLE rawlog CHANGE apikey_id apikey INT(10) unsigned NOT NULL');
        $this->addSql('ALTER TABLE rawlog CHANGE event_time eventtime DATETIME DEFAULT NULL');
    }
}
