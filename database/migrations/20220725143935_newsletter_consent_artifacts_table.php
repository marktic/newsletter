<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class NewsletterConsentArtifactsTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $table_name = 'mkt_newsletter_consent_artifacts';
        $exists = $this->hasTable($table_name);
        if ($exists) {
            return;
        }
        $table = $this->table($table_name);
        $table
            ->addColumn('consent_id', 'integer', ['null' => true])
            ->addColumn('statement_id', 'integer', ['null' => true])
            ->addColumn('contact_id', 'integer', ['null' => true])
            ->addColumn('subscription_id', 'integer', ['null' => true])
            ->addColumn('updated_at', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'update' => 'CURRENT_TIMESTAMP',
            ])
            ->addColumn('created_at', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
            ]);


        $table
            ->addIndex(['consent_id'])
            ->addIndex(['statement_id'])
            ->addIndex(['contact_id'])
            ->addIndex(['subscription_id'])
            ->save();

        $table
            ->addForeignKey(
                'consent_id',
                'mkt_newsletter_consents',
                'id',
                ['constraint' => 'mkt_newsletter_consents_consent_id', 'delete' => 'NO_ACTION', 'update' => 'NO_ACTION']
            )
            ->addForeignKey(
                'statement_id',
                'mkt_newsletter_consent_statements',
                'id',
                ['constraint' => 'mkt_newsletter_consent_statements_statement_id', 'delete' => 'NO_ACTION', 'update' => 'NO_ACTION']
            )
            ->addForeignKey(
                'contact_id',
                'mkt_newsletter_contacts',
                'id',
                ['constraint' => 'mkt_newsletter_contacts_contact_id', 'delete' => 'NO_ACTION', 'update' => 'NO_ACTION']
            )
            ->addForeignKey(
                'subscription_id',
                'mkt_newsletter_subscriptions',
                'id',
                ['constraint' => 'mkt_newsletter_subscriptions_subscription_id', 'delete' => 'NO_ACTION', 'update' => 'NO_ACTION']
            )
            ->save();
    }
}
