<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class NewsletterFiltersTable extends AbstractMigration
{
    public function change(): void
    {
        $table_name = 'mkt_newsletter_filters';
        $exists = $this->hasTable($table_name);
        if ($exists) {
            return;
        }
        $table = $this->table($table_name);
        $table
            ->addColumn('id_newsletter', 'integer', ['null' => false, 'signed' => false])
            ->addColumn('type', 'string', ['limit' => 100, 'null' => true])
            ->addColumn('values', 'json', ['null' => true]);

        $table
            ->addIndex(['id_newsletter']);

        $table->save();
    }
}
