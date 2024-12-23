<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241219162907 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE followers (user_id INT NOT NULL, follower_id INT NOT NULL, PRIMARY KEY(user_id, follower_id))');
        $this->addSql('CREATE INDEX IDX_8408FDA7A76ED395 ON followers (user_id)');
        $this->addSql('CREATE INDEX IDX_8408FDA7AC24F853 ON followers (follower_id)');
        $this->addSql('ALTER TABLE followers ADD CONSTRAINT FK_8408FDA7A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE followers ADD CONSTRAINT FK_8408FDA7AC24F853 FOREIGN KEY (follower_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE followers DROP CONSTRAINT FK_8408FDA7A76ED395');
        $this->addSql('ALTER TABLE followers DROP CONSTRAINT FK_8408FDA7AC24F853');
        $this->addSql('DROP TABLE followers');
    }
}
