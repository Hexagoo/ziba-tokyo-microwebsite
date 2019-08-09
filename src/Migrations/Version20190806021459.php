<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190806021459 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE activityHasMedias_user (user_id INT NOT NULL, activityHasMedias_id INT NOT NULL, INDEX IDX_DFB2D0A032A199B6 (activityHasMedias_id), INDEX IDX_DFB2D0A0A76ED395 (user_id), PRIMARY KEY(activityHasMedias_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE activityHasMedias_user ADD CONSTRAINT FK_DFB2D0A032A199B6 FOREIGN KEY (activityHasMedias_id) REFERENCES activity_has_media (id)');
        $this->addSql('ALTER TABLE activityHasMedias_user ADD CONSTRAINT FK_DFB2D0A0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user CHANGE project_name project_name VARCHAR(64) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE activityHasMedias_user');
        $this->addSql('ALTER TABLE user CHANGE project_name project_name VARCHAR(191) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
