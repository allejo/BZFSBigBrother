<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220807010017 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE latency_record (id INT UNSIGNED AUTO_INCREMENT NOT NULL, address_id INT UNSIGNED NOT NULL, server_instance_id INT UNSIGNED NOT NULL, jitter INT NOT NULL, lag_ms INT NOT NULL, packet_loss DOUBLE PRECISION NOT NULL, event_time DATETIME NOT NULL, INDEX IDX_448D409CF5B7AF75 (address_id), INDEX IDX_448D409C72318BC6 (server_instance_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE server_instance (id INT UNSIGNED AUTO_INCREMENT NOT NULL, api_key_id INT UNSIGNED NOT NULL, hostname VARCHAR(255) NOT NULL, port INT NOT NULL, INDEX IDX_809530628BE312B3 (api_key_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE latency_record ADD CONSTRAINT FK_448D409CF5B7AF75 FOREIGN KEY (address_id) REFERENCES addresses (id)');
        $this->addSql('ALTER TABLE latency_record ADD CONSTRAINT FK_448D409C72318BC6 FOREIGN KEY (server_instance_id) REFERENCES server_instance (id)');
        $this->addSql('ALTER TABLE server_instance ADD CONSTRAINT FK_809530628BE312B3 FOREIGN KEY (api_key_id) REFERENCES apikeys (id)');
        $this->addSql('ALTER TABLE joins RENAME INDEX idx_391e9a45adce7eb TO IDX_391E9A43A174AB4');
        $this->addSql('ALTER TABLE joins RENAME INDEX idx_391e9a440acbfdd TO IDX_391E9A4F5B7AF75');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE latency_record DROP FOREIGN KEY FK_448D409C72318BC6');
        $this->addSql('DROP TABLE latency_record');
        $this->addSql('DROP TABLE server_instance');
        $this->addSql('ALTER TABLE joins RENAME INDEX idx_391e9a4f5b7af75 TO IDX_391E9A440ACBFDD');
        $this->addSql('ALTER TABLE joins RENAME INDEX idx_391e9a43a174ab4 TO IDX_391E9A45ADCE7EB');
    }
}
