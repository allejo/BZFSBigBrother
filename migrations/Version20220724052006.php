<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220724052006 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // Drop FKs first
        $this->addSql('ALTER TABLE joins DROP FOREIGN KEY FK_391E9A440ACBFDD');
        $this->addSql('ALTER TABLE joins DROP FOREIGN KEY FK_391E9A45ADCE7EB');
        $this->addSql('ALTER TABLE rawlog DROP FOREIGN KEY FK_D5EC7A66B84757A1');

        $this->addSql('ALTER TABLE addresses CHANGE addressid id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT');
        $this->addSql('ALTER TABLE apikeys CHANGE apikeyid id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT');
        $this->addSql('ALTER TABLE callsigns CHANGE callsignid id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT');
        $this->addSql('ALTER TABLE joins CHANGE joinid id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT');
        $this->addSql('ALTER TABLE joins ADD CONSTRAINT FK_391E9A440ACBFDD FOREIGN KEY (addressid) REFERENCES addresses (id)');
        $this->addSql('ALTER TABLE joins ADD CONSTRAINT FK_391E9A45ADCE7EB FOREIGN KEY (callsignid) REFERENCES callsigns (id)');
        $this->addSql('ALTER TABLE rawlog CHANGE rawlogid id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT');
        $this->addSql('ALTER TABLE rawlog ADD CONSTRAINT FK_D5EC7A66B84757A1 FOREIGN KEY (apikey) REFERENCES apikeys (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE joins DROP FOREIGN KEY FK_391E9A440ACBFDD');
        $this->addSql('ALTER TABLE joins DROP FOREIGN KEY FK_391E9A45ADCE7EB');
        $this->addSql('ALTER TABLE rawlog DROP FOREIGN KEY FK_D5EC7A66B84757A1');

        $this->addSql('ALTER TABLE addresses CHANGE id addressid INT UNSIGNED AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE apikeys CHANGE id apikeyid INT UNSIGNED AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE callsigns CHANGE id callsignid INT UNSIGNED AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE joins CHANGE id joinid INT UNSIGNED AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE joins ADD CONSTRAINT FK_391E9A45ADCE7EB FOREIGN KEY (callsignid) REFERENCES callsigns (callsignid)');
        $this->addSql('ALTER TABLE joins ADD CONSTRAINT FK_391E9A440ACBFDD FOREIGN KEY (addressid) REFERENCES addresses (addressid)');
        $this->addSql('ALTER TABLE rawlog CHANGE id rawlogid INT UNSIGNED AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE rawlog ADD CONSTRAINT FK_D5EC7A66B84757A1 FOREIGN KEY (apikey) REFERENCES apikeys (apikeyid)');
    }
}
