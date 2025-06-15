<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateQueueJobs extends BaseMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/migrations/4/en/migrations.html#the-change-method
     * @return void
     */
    public function change(): void
    {
        $table = $this->table('queue_jobs');
        $table
            ->addColumn('title', 'string', [
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('task', 'string', [
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('data', 'text', [
                'null' => true,
            ])
            ->addColumn('status', 'string', [
                'limit' => 50,
                'default' => 'queued',
                'null' => true,
            ])
            ->addColumn('progress', 'integer', [
                'default' => 0,
                'null' => true,
            ])
            ->addColumn('attempts', 'integer', [
                'default' => 0,
                'null' => true,
            ])
            ->addColumn('max_attempts', 'integer', [
                'default' => 3,
                'null' => true,
            ])
            ->addColumn('error', 'text', [
                'null' => true,
            ])
            ->addColumn('trace', 'text', [
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'null' => true,
            ])
            ->create();
    }
}
