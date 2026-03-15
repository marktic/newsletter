<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class NewsletterNewslettersGrapesjsData extends AbstractMigration
{
    public function change(): void
    {
        $table_name = 'mkt_newsletter_newsletters';
        if (!$this->hasTable($table_name)) {
            return;
        }
        $table = $this->table($table_name);
        if ($table->hasColumn('grapesjs_data')) {
            return;
        }
        $table
            ->addColumn('grapesjs_data', 'text', ['null' => true, 'after' => 'content'])
            ->save();
    }
}
