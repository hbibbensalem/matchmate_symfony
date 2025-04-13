<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class VersionAddDescriptionUser extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add description_user column to the User table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user ADD description_user VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP description_user');
    }
}
