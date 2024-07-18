<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240717134547 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add created_at column to order table and handle foreign key constraints for user table';
    }

    public function up(Schema $schema): void
    {
        // Ajouter la colonne created_at à la table order
        $this->addSql('ALTER TABLE `order` ADD created_at DATE NOT NULL');
        
        // Supprimer la contrainte étrangère existante si elle existe
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY IF EXISTS FK_8D93D6498486F9AC');
        
        // Ajouter de nouveau la contrainte avec un nom unique
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649_NEW FOREIGN KEY (adress_id) REFERENCES adress (id)');
        
        // Créer l'index pour la nouvelle contrainte
        $this->addSql('CREATE INDEX IDX_8D93D6498486F9AC ON user (adress_id)');
    }

    public function down(Schema $schema): void
    {
        // Supprimer la colonne created_at de la table order
        $this->addSql('ALTER TABLE `order` DROP created_at');
        
        // Supprimer la nouvelle contrainte
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649_NEW');
        
        // Supprimer l'index de la nouvelle contrainte
        $this->addSql('DROP INDEX IDX_8D93D6498486F9AC ON user');
        
        // Ré-ajouter l'ancienne contrainte
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6498486F9AC FOREIGN KEY (adress_id) REFERENCES adress (id)');
    }
}