<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240522210732 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` ADD klant_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993983C427B2F FOREIGN KEY (klant_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_F52993983C427B2F ON `order` (klant_id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649558FBEB9 FOREIGN KEY (purchase_id) REFERENCES `order` (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649558FBEB9 ON user (purchase_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993983C427B2F');
        $this->addSql('DROP INDEX IDX_F52993983C427B2F ON `order`');
        $this->addSql('ALTER TABLE `order` DROP klant_id');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649558FBEB9');
        $this->addSql('DROP INDEX IDX_8D93D649558FBEB9 ON user');
    }
}
