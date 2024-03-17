<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240305131650 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE vinyl_category (vinyl_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_B222E133FFFF645 (vinyl_id), INDEX IDX_B222E1312469DE2 (category_id), PRIMARY KEY(vinyl_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE vinyl_category ADD CONSTRAINT FK_B222E133FFFF645 FOREIGN KEY (vinyl_id) REFERENCES vinyl (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE vinyl_category ADD CONSTRAINT FK_B222E1312469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE vinyl_category DROP FOREIGN KEY FK_B222E133FFFF645');
        $this->addSql('ALTER TABLE vinyl_category DROP FOREIGN KEY FK_B222E1312469DE2');
        $this->addSql('DROP TABLE vinyl_category');
    }
}
