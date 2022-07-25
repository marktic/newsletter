<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class NewsletterConsentStatementsTable extends AbstractMigration
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
        $table_name = 'mkt_newsletter_consent_statements';
        $exists = $this->hasTable($table_name);
        if ($exists) {
            return;
        }
        $table = $this->table($table_name);
        $table
            ->addColumn('consent_id', 'integer', ['null' => true])
            ->addColumn('hash', 'string', ['null' => true])
            ->addColumn('text', 'text', ['null' => true])
            ->addColumn('updated_at', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'update' => 'CURRENT_TIMESTAMP',
            ])
            ->addColumn('created_at', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
            ]);


        $table
            ->addIndex(['consent_id'])
            ->addIndex(['hash'], ['unique' => true]);

        $table->save();
    }
}
