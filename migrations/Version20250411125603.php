<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250411125603 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', available_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commande CHANGE id_user id_user VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D391C87D5 FOREIGN KEY (idProduit) REFERENCES produit (id_produit) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA76B3CA4B
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_EVENT_USER ON event
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event CHANGE id_user id_user VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE listinscri DROP FOREIGN KEY FK_LISTINSCRI_USER
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE listinscri DROP FOREIGN KEY FK_F1ECF601BCC4FBD3
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_LISTINSCRI_USER ON listinscri
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_LISTINSCRI_MATCH ON listinscri
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE listinscri CHANGE id_user id_user VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE match1 DROP FOREIGN KEY FK_F3CC40FD6B3CA4B
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_MATCH1_USER ON match1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE match1 CHANGE id_user id_user VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE participation DROP FOREIGN KEY FK_AB55E24F6B3CA4B
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_PARTICIPATION_USER ON participation
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_PARTICIPATION_EVENT ON participation
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE participation CHANGE id_user id_user VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_RECLAMATION_SUPPORT ON reclamation
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE regime DROP FOREIGN KEY FK_AA864A7C6B3CA4B
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_REGIME_USER ON regime
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE regime CHANGE id_user id_user VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE suivi DROP FOREIGN KEY FK_2EBCCA8F6B3CA4B
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_SUIVI_USER ON suivi
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_SUIVI_REGIME ON suivi
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE suivi CHANGE id_user id_user VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user CHANGE token_expiration token_expiration DATETIME NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D391C87D5
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commande CHANGE id_user id_user VARCHAR(255) DEFAULT 'NULL'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event CHANGE id_user id_user VARCHAR(255) DEFAULT 'NULL'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_EVENT_USER ON event (id_user)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE listinscri CHANGE id_user id_user VARCHAR(255) DEFAULT 'NULL'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_LISTINSCRI_USER ON listinscri (id_user)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_LISTINSCRI_MATCH ON listinscri (matchId)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE match1 CHANGE id_user id_user VARCHAR(255) DEFAULT 'NULL'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_MATCH1_USER ON match1 (id_user)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE participation CHANGE id_user id_user VARCHAR(255) DEFAULT 'NULL'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_PARTICIPATION_USER ON participation (id_user)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_PARTICIPATION_EVENT ON participation (idevent)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_RECLAMATION_SUPPORT ON reclamation (id_s)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE regime CHANGE id_user id_user VARCHAR(255) DEFAULT 'NULL'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_REGIME_USER ON regime (id_user)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE suivi CHANGE id_user id_user VARCHAR(255) DEFAULT 'NULL'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_SUIVI_USER ON suivi (id_user)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_SUIVI_REGIME ON suivi (regime_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user CHANGE token_expiration token_expiration DATETIME DEFAULT 'NULL'
        SQL);
    }
}
