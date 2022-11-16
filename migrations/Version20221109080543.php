<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221109080543 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE categorie ADD etablissement_id INT NOT NULL');
        $this->addSql('ALTER TABLE categorie ADD CONSTRAINT FK_497DD634FF631228 FOREIGN KEY (etablissement_id) REFERENCES etablissement (id)');
        $this->addSql('CREATE INDEX IDX_497DD634FF631228 ON categorie (etablissement_id)');
        $this->addSql('ALTER TABLE etablissement DROP categorie, CHANGE description description LONGTEXT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE categorie DROP FOREIGN KEY FK_497DD634FF631228');
        $this->addSql('DROP INDEX IDX_497DD634FF631228 ON categorie');
        $this->addSql('ALTER TABLE categorie DROP etablissement_id');
        $this->addSql('ALTER TABLE etablissement ADD categorie LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', CHANGE description description VARCHAR(255) NOT NULL');
    }
}
