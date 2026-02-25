<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class NewsletterNewslettersTable extends AbstractMigration
{
    public function change(): void
    {
        $table_name = 'mkt_newsletter_newsletters';
        $exists = $this->hasTable($table_name);
        if ($exists) {
            return;
        }
        $table = $this->table($table_name);
        $table
            ->addColumn('owner_id', 'integer', ['null' => true])
            ->addColumn('owner', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('uuid', 'string', ['limit' => 36, 'null' => true])
            ->addColumn('type', 'string', ['limit' => 100, 'null' => true])
            ->addColumn('list_id', 'integer', ['null' => true, 'signed' => false])
            ->addColumn('name', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('subject', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('content', 'text', ['null' => true])
            ->addColumn('status', 'enum', ['values' => ['draft', 'scheduled', 'sending', 'sent'], 'default' => 'draft', 'null' => false])
            ->addColumn('updated_at', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'update' => 'CURRENT_TIMESTAMP',
            ])
            ->addColumn('created_at', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
            ]);

        $table
            ->addIndex(['owner_id'])
            ->addIndex(['owner'])
            ->addIndex(['list_id'])
            ->addIndex(['uuid'], ['unique' => true]);

        $table->save();
    }
}
