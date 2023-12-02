<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231202145441 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE accounting_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE customer_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE invoice_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE media_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE quotation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE accounting (id INT NOT NULL, total_quotations INT DEFAULT NULL, total_invoices INT DEFAULT NULL, total_income NUMERIC(10, 2) DEFAULT NULL, total_paid_invoices INT DEFAULT NULL, total_valid_quotations INT DEFAULT NULL, total_late_invoices INT DEFAULT NULL, total_new_customers INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE customer (id INT NOT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, email VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE invoice (id INT NOT NULL, quotation_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expires_in DATE DEFAULT NULL, title VARCHAR(255) NOT NULL, amount NUMERIC(8, 2) DEFAULT NULL, option VARCHAR(255) DEFAULT NULL, option_price NUMERIC(8, 2) DEFAULT NULL, quantity INT DEFAULT NULL, total_with_out_taxes NUMERIC(8, 2) DEFAULT NULL, taxes NUMERIC(4, 2) DEFAULT NULL, total_with_taxes NUMERIC(8, 2) DEFAULT NULL, is_valid BOOLEAN DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_90651744B4EA4E60 ON invoice (quotation_id)');
        $this->addSql('COMMENT ON COLUMN invoice.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE media (id INT NOT NULL, uploaded_by_id INT NOT NULL, name VARCHAR(255) NOT NULL, path TEXT NOT NULL, type VARCHAR(50) NOT NULL, size INT NOT NULL, description TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6A2CA10CA2B28FE8 ON media (uploaded_by_id)');
        $this->addSql('COMMENT ON COLUMN media.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE quotation (id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expires_in DATE DEFAULT NULL, title VARCHAR(255) NOT NULL, amount NUMERIC(8, 2) DEFAULT NULL, option VARCHAR(255) DEFAULT NULL, option_price NUMERIC(8, 2) DEFAULT NULL, quantity INT DEFAULT NULL, total_with_out_taxes NUMERIC(8, 2) DEFAULT NULL, taxes NUMERIC(4, 2) DEFAULT NULL, total_with_taxes NUMERIC(8, 2) DEFAULT NULL, is_valid BOOLEAN DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN quotation.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, accountings_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, job VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('CREATE INDEX IDX_8D93D649A423299F ON "user" (accountings_id)');
        $this->addSql('CREATE TABLE user_quotation (user_id INT NOT NULL, quotation_id INT NOT NULL, PRIMARY KEY(user_id, quotation_id))');
        $this->addSql('CREATE INDEX IDX_47AA005DA76ED395 ON user_quotation (user_id)');
        $this->addSql('CREATE INDEX IDX_47AA005DB4EA4E60 ON user_quotation (quotation_id)');
        $this->addSql('CREATE TABLE user_invoice (user_id INT NOT NULL, invoice_id INT NOT NULL, PRIMARY KEY(user_id, invoice_id))');
        $this->addSql('CREATE INDEX IDX_C868094EA76ED395 ON user_invoice (user_id)');
        $this->addSql('CREATE INDEX IDX_C868094E2989F1FD ON user_invoice (invoice_id)');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_90651744B4EA4E60 FOREIGN KEY (quotation_id) REFERENCES quotation (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10CA2B28FE8 FOREIGN KEY (uploaded_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D649A423299F FOREIGN KEY (accountings_id) REFERENCES accounting (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_quotation ADD CONSTRAINT FK_47AA005DA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_quotation ADD CONSTRAINT FK_47AA005DB4EA4E60 FOREIGN KEY (quotation_id) REFERENCES quotation (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_invoice ADD CONSTRAINT FK_C868094EA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_invoice ADD CONSTRAINT FK_C868094E2989F1FD FOREIGN KEY (invoice_id) REFERENCES invoice (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE accounting_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE customer_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE invoice_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE media_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE quotation_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        $this->addSql('ALTER TABLE invoice DROP CONSTRAINT FK_90651744B4EA4E60');
        $this->addSql('ALTER TABLE media DROP CONSTRAINT FK_6A2CA10CA2B28FE8');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D649A423299F');
        $this->addSql('ALTER TABLE user_quotation DROP CONSTRAINT FK_47AA005DA76ED395');
        $this->addSql('ALTER TABLE user_quotation DROP CONSTRAINT FK_47AA005DB4EA4E60');
        $this->addSql('ALTER TABLE user_invoice DROP CONSTRAINT FK_C868094EA76ED395');
        $this->addSql('ALTER TABLE user_invoice DROP CONSTRAINT FK_C868094E2989F1FD');
        $this->addSql('DROP TABLE accounting');
        $this->addSql('DROP TABLE customer');
        $this->addSql('DROP TABLE invoice');
        $this->addSql('DROP TABLE media');
        $this->addSql('DROP TABLE quotation');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE user_quotation');
        $this->addSql('DROP TABLE user_invoice');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
