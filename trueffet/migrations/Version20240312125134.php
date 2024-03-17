<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240312125134 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_vinyl (user_id INT NOT NULL, vinyl_id INT NOT NULL, INDEX IDX_ECECA845A76ED395 (user_id), INDEX IDX_ECECA8453FFFF645 (vinyl_id), PRIMARY KEY(user_id, vinyl_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_vinyl ADD CONSTRAINT FK_ECECA845A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_vinyl ADD CONSTRAINT FK_ECECA8453FFFF645 FOREIGN KEY (vinyl_id) REFERENCES vinyl (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_vinyl DROP FOREIGN KEY FK_ECECA845A76ED395');
        $this->addSql('ALTER TABLE user_vinyl DROP FOREIGN KEY FK_ECECA8453FFFF645');
        $this->addSql('DROP TABLE user_vinyl');
    }
}
