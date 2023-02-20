<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230219003416 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE order_article_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE "order" DROP CONSTRAINT fk_f529939819eb6921');
        $this->addSql('DROP INDEX idx_f529939819eb6921');
        $this->addSql('ALTER TABLE "order" DROP client_id');
        $this->addSql('ALTER TABLE order_article DROP CONSTRAINT FK_F440A72D8D9F6D38');
        $this->addSql('ALTER TABLE order_article DROP CONSTRAINT FK_F440A72D7294869C');
        $this->addSql('DROP INDEX "primary"');
        $this->addSql('ALTER TABLE order_article ADD id INT NOT NULL');
        $this->addSql('ALTER TABLE order_article ADD quantity INT NOT NULL');
        $this->addSql('ALTER TABLE order_article ADD CONSTRAINT FK_F440A72D8D9F6D38 FOREIGN KEY (order_id) REFERENCES "order" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_article ADD CONSTRAINT FK_F440A72D7294869C FOREIGN KEY (article_id) REFERENCES "article" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_article ADD PRIMARY KEY (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE order_article_id_seq CASCADE');
        $this->addSql('ALTER TABLE order_article DROP CONSTRAINT fk_f440a72d8d9f6d38');
        $this->addSql('ALTER TABLE order_article DROP CONSTRAINT fk_f440a72d7294869c');
        $this->addSql('DROP INDEX order_article_pkey');
        $this->addSql('ALTER TABLE order_article DROP id');
        $this->addSql('ALTER TABLE order_article DROP quantity');
        $this->addSql('ALTER TABLE order_article ADD CONSTRAINT fk_f440a72d8d9f6d38 FOREIGN KEY (order_id) REFERENCES "order" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_article ADD CONSTRAINT fk_f440a72d7294869c FOREIGN KEY (article_id) REFERENCES article (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_article ADD PRIMARY KEY (order_id, article_id)');
        $this->addSql('ALTER TABLE "order" ADD client_id INT NOT NULL');
        $this->addSql('ALTER TABLE "order" ADD CONSTRAINT fk_f529939819eb6921 FOREIGN KEY (client_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_f529939819eb6921 ON "order" (client_id)');
    }
}
