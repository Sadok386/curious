<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180424145957 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_time_projet DROP FOREIGN KEY FK_A0F81ACA79F37AE5');
        $this->addSql('ALTER TABLE user_time_projet DROP FOREIGN KEY FK_A0F81ACA80F43E55');
        $this->addSql('DROP INDEX IDX_A0F81ACA79F37AE5 ON user_time_projet');
        $this->addSql('DROP INDEX IDX_A0F81ACA80F43E55 ON user_time_projet');
        $this->addSql('ALTER TABLE user_time_projet ADD user_id INT NOT NULL, ADD projet_id INT NOT NULL, DROP id_user_id, DROP id_projet_id');
        $this->addSql('ALTER TABLE user_time_projet ADD CONSTRAINT FK_A0F81ACAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_time_projet ADD CONSTRAINT FK_A0F81ACAC18272 FOREIGN KEY (projet_id) REFERENCES projet (id)');
        $this->addSql('CREATE INDEX IDX_A0F81ACAA76ED395 ON user_time_projet (user_id)');
        $this->addSql('CREATE INDEX IDX_A0F81ACAC18272 ON user_time_projet (projet_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_time_projet DROP FOREIGN KEY FK_A0F81ACAA76ED395');
        $this->addSql('ALTER TABLE user_time_projet DROP FOREIGN KEY FK_A0F81ACAC18272');
        $this->addSql('DROP INDEX IDX_A0F81ACAA76ED395 ON user_time_projet');
        $this->addSql('DROP INDEX IDX_A0F81ACAC18272 ON user_time_projet');
        $this->addSql('ALTER TABLE user_time_projet ADD id_user_id INT NOT NULL, ADD id_projet_id INT NOT NULL, DROP user_id, DROP projet_id');
        $this->addSql('ALTER TABLE user_time_projet ADD CONSTRAINT FK_A0F81ACA79F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_time_projet ADD CONSTRAINT FK_A0F81ACA80F43E55 FOREIGN KEY (id_projet_id) REFERENCES projet (id)');
        $this->addSql('CREATE INDEX IDX_A0F81ACA79F37AE5 ON user_time_projet (id_user_id)');
        $this->addSql('CREATE INDEX IDX_A0F81ACA80F43E55 ON user_time_projet (id_projet_id)');
    }
}
