<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240630111901 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE inventory (id INT AUTO_INCREMENT NOT NULL, store_id INT DEFAULT NULL, game_id INT NOT NULL, quantity INT NOT NULL, INDEX IDX_B12D4A36B092A811 (store_id), INDEX IDX_B12D4A36E48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, reservation_id INT DEFAULT NULL, order_date DATE NOT NULL, pickup_date DATE NOT NULL, status VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_F5299398B83297E7 (reservation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, game_id INT DEFAULT NULL, user_id INT DEFAULT NULL, store_id INT DEFAULT NULL, date_reservation DATE NOT NULL, status VARCHAR(255) NOT NULL, quantity INT NOT NULL, INDEX IDX_42C84955E48FD905 (game_id), INDEX IDX_42C84955A76ED395 (user_id), INDEX IDX_42C84955B092A811 (store_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE inventory ADD CONSTRAINT FK_B12D4A36B092A811 FOREIGN KEY (store_id) REFERENCES store (id)');
        $this->addSql('ALTER TABLE inventory ADD CONSTRAINT FK_B12D4A36E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398B83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955B092A811 FOREIGN KEY (store_id) REFERENCES store (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inventory DROP FOREIGN KEY FK_B12D4A36B092A811');
        $this->addSql('ALTER TABLE inventory DROP FOREIGN KEY FK_B12D4A36E48FD905');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398B83297E7');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955E48FD905');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955A76ED395');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955B092A811');
        $this->addSql('DROP TABLE inventory');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE reservation');
    }
}
