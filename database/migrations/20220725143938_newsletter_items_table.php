<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class NewsletterItemsTable extends AbstractMigration
{
    public function change(): void
    {
        $table_name = 'mkt_newsletter_items';
        $exists = $this->hasTable($table_name);
        if ($exists) {
            return;
        }
        $table = $this->table($table_name);
        $table
            ->addColumn('id_newsletter', 'integer', ['null' => false, 'signed' => false])
            ->addColumn('record_id', 'integer', ['null' => true])
            ->addColumn('record_type', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('status', 'enum', ['values' => ['pending', 'sent', 'error'], 'default' => 'pending', 'null' => false]);

        $table
            ->addIndex(['id_newsletter'])
            ->addIndex(['record_id'])
            ->addIndex(['record_type']);

        $table->save();
    }
}
