<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240630151217 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398B83297E7');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955B092A811');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955E48FD905');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955A76ED395');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('ALTER TABLE adress CHANGE street street VARCHAR(255) NOT NULL');
        $this->addSql('DROP INDEX UNIQ_F5299398B83297E7 ON `order`');
        $this->addSql('ALTER TABLE `order` ADD store_id INT DEFAULT NULL, ADD quantity INT NOT NULL, CHANGE reservation_id game_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398B092A811 FOREIGN KEY (store_id) REFERENCES store (id)');
        $this->addSql('CREATE INDEX IDX_F5299398E48FD905 ON `order` (game_id)');
        $this->addSql('CREATE INDEX IDX_F5299398B092A811 ON `order` (store_id)');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6498486F9AC');
        $this->addSql('DROP INDEX IDX_8D93D6498486F9AC ON user');
        $this->addSql('ALTER TABLE user ADD updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', DROP adress_id, CHANGE email email VARCHAR(180) NOT NULL, CHANGE role roles JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON user (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, game_id INT DEFAULT NULL, user_id INT DEFAULT NULL, store_id INT DEFAULT NULL, date_reservation DATE NOT NULL, status VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, quantity INT NOT NULL, INDEX IDX_42C84955B092A811 (store_id), INDEX IDX_42C84955E48FD905 (game_id), INDEX IDX_42C84955A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955B092A811 FOREIGN KEY (store_id) REFERENCES store (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE adress CHANGE street street VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398E48FD905');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398B092A811');
        $this->addSql('DROP INDEX IDX_F5299398E48FD905 ON `order`');
        $this->addSql('DROP INDEX IDX_F5299398B092A811 ON `order`');
        $this->addSql('ALTER TABLE `order` ADD reservation_id INT DEFAULT NULL, DROP game_id, DROP store_id, DROP quantity');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398B83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F5299398B83297E7 ON `order` (reservation_id)');
        $this->addSql('DROP INDEX UNIQ_IDENTIFIER_EMAIL ON user');
        $this->addSql('ALTER TABLE user ADD adress_id INT NOT NULL, DROP updated_at, CHANGE email email VARCHAR(255) NOT NULL, CHANGE roles role JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6498486F9AC FOREIGN KEY (adress_id) REFERENCES adress (id)');
        $this->addSql('CREATE INDEX IDX_8D93D6498486F9AC ON user (adress_id)');
    }
}
