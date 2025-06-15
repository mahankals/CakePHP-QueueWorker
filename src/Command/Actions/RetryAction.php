<?php

declare(strict_types=1);

namespace QueueWorker\Command\Actions;

use Cake\Command\Command;
use Cake\Console\ConsoleIo;
use Cake\ORM\Locator\LocatorAwareTrait;

class RetryAction
{
  use LocatorAwareTrait;

  private \Cake\ORM\Table $JobsTable;

  public function __construct()
  {
    $this->JobsTable = $this->fetchTable('QueueWorker.QueueJobs');
  }

  public function run(ConsoleIo $io, ?string $jobId = null): int
  {
    $io->out("🔁 Retrying queue worker...");
    // $jobsTable = $this->fetchTable('QueueWorker.QueueJobs');

    // Retry for single job
    if ($jobId) {
      $job = $this->JobsTable->find()->where(['id' => $jobId, 'status' => 'failed'])->first();

      if ($job) {
        $this->reQueued($job);
        // $job->status = 'queued';
        // $job->attempts += 1;
        // $jobsTable->save($job);
        $io->out("✅ Retried job ID {$job->id}");
      } else {
        $io->err("❌ No failed job found with ID {$jobId}");
      }

      return Command::CODE_SUCCESS;
    }

    // Retry all jobs
    $jobs = $this->JobsTable->find()
      ->where(['status' => 'failed'])
      ->all();
    foreach ($jobs as $job) {
      $this->reQueued($job);
      // $job->status = 'queued';
      // $job->attempts = 0;
      // $job->error = null;
      // $job->trace = null;
      // $jobsTable->save($job);
      $io->out("✅ Retried job ID: {$job->id}");
    }

    return Command::CODE_SUCCESS;
  }

  private function reQueued($job)
  {
    $job->status = 'queued';
    $job->attempts = 0;
    $job->error = null;
    $job->trace = null;
    $this->JobsTable->save($job);
  }
}
