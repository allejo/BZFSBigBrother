<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220724030537 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // If there are any rows where `callsign` is null as these are invalid records
        $this->addSql('DELETE FROM rawlog WHERE callsign IS NULL');
        $this->addSql('DELETE FROM callsigns WHERE callsign IS NULL');
        $this->addSql('DELETE FROM joins WHERE callsignid IS NULL or addressid = 0');

        $this->addSql('ALTER TABLE joins CHANGE callsignid callsignid INT UNSIGNED NOT NULL, CHANGE addressid addressid INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE joins ADD CONSTRAINT FK_391E9A45ADCE7EB FOREIGN KEY (callsignid) REFERENCES callsigns (callsignid)');
        $this->addSql('ALTER TABLE joins ADD CONSTRAINT FK_391E9A440ACBFDD FOREIGN KEY (addressid) REFERENCES addresses (addressid)');
        $this->addSql('CREATE INDEX IDX_391E9A440ACBFDD ON joins (addressid)');
        $this->addSql('CREATE INDEX IDX_391E9A45ADCE7EB ON joins (callsignid)');
        $this->addSql('ALTER TABLE rawlog CHANGE apikey apikey INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE rawlog ADD CONSTRAINT FK_D5EC7A66B84757A1 FOREIGN KEY (apikey) REFERENCES apikeys (apikeyid)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE joins DROP FOREIGN KEY FK_391E9A45ADCE7EB');
        $this->addSql('ALTER TABLE joins DROP FOREIGN KEY FK_391E9A440ACBFDD');
        $this->addSql('DROP INDEX IDX_391E9A440ACBFDD ON joins');
        $this->addSql('DROP INDEX IDX_391E9A45ADCE7EB ON joins');
        $this->addSql('ALTER TABLE joins CHANGE callsignid callsignid INT UNSIGNED DEFAULT NULL, CHANGE addressid addressid INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE rawlog DROP FOREIGN KEY FK_D5EC7A66B84757A1');
        $this->addSql('ALTER TABLE rawlog CHANGE apikey apikey VARCHAR(40) NOT NULL');
    }
}
