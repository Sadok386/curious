<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180420131708 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_time_projet (id INT AUTO_INCREMENT NOT NULL, id_user_id INT NOT NULL, id_projet_id INT NOT NULL, time DOUBLE PRECISION DEFAULT NULL, INDEX IDX_A0F81ACA79F37AE5 (id_user_id), INDEX IDX_A0F81ACA80F43E55 (id_projet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_time_projet ADD CONSTRAINT FK_A0F81ACA79F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_time_projet ADD CONSTRAINT FK_A0F81ACA80F43E55 FOREIGN KEY (id_projet_id) REFERENCES projet (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE user_time_projet');
    }
}
