<?php

declare(strict_types=1);

namespace QueueWorker\Command\Actions;

use Cake\Command\Command;
use Cake\Console\ConsoleIo;
use Cake\ORM\Locator\LocatorAwareTrait;

class StartAction
{
  use LocatorAwareTrait;

  private bool $stopRequested = false;
  private const STOP_FILE = TMP . 'cake_queue_stop.flag';

  public function run(ConsoleIo $io, ?bool $once = null): int
  {
    // Setup Ctrl+C signal handler
    if (function_exists('pcntl_signal')) {
      pcntl_signal(SIGINT, function () use ($io) {
        $io->out("\nTermination requested. Stopping worker gracefully...");
        $this->stopRequested = true;
      });
    }

    $io->out('Starting queue worker...');
    $frames = ['.     ', '..', '...', '....', '.....'];
    $frameIndex = 0;

    $jobsTable = $this->getTableLocator()->get('QueueWorker.QueueJobs');
    $jobsTable->updateAll(
      [
        'status' => 'queued',
        'attempts' => 0
      ],
      ['status' => 'running']
    );

    while (!$this->stopRequested) {
      if (function_exists('pcntl_signal_dispatch')) {
        pcntl_signal_dispatch();
      }

      if (file_exists(self::STOP_FILE)) {
        $io->out("🛑 Stop flag detected. Shutting down...");
        unlink(self::STOP_FILE);
        break;
      }

      $job = $jobsTable->find()
        ->where(['status' => 'queued'])
        ->first();

      if ($job) {
        $attempt = $job->attempts + 1;
        $title = "[{$job->id}] " . $job->title ?? $job->job;
        $io->out("\nProcessing job : {$title} : attempt {$attempt}");

        $job->status = 'running';
        $job->attempts += 1;
        $jobsTable->save($job);

        $jobPath = ROOT . DS . 'src' . DS . 'Jobs' . DS . "{$job->job}Job.php";
        if (!file_exists($jobPath)) {
          $job->status = 'failed';
          $job->error = "Job class file not found: {$jobPath}";
          $jobsTable->save($job);
          continue;
        }

        try {
          require_once $jobPath;
          $jobClassName = "\\App\\Jobs\\{$job->job}Job";

          $jobClass = new $jobClassName();
          $jobClass->run(json_decode($job->data, true));

          $job->status = 'completed';
          $job->error = null;
        } catch (\Throwable $e) {
          $error = $e->getMessage();
          $file = $e->getFile();
          $line = $e->getLine();
          $trace = $e->getTraceAsString();

          /** @var \Throwable $e */
          \Cake\Log\Log::error(sprintf(
            "[%s] %s in %s on line %d\nStack trace:\n%s",
            get_class($e),
            $error,
            $file,
            $line,
            $trace
          ));

          if (((int) $job->attempts < (int) $job->max_attempts)) {
            $job->status = 'queued'; // requeue for retry

            $job->error = "[Retry] {$error} in {$file} on line {$line}";
            // sleep(60);
          } else {
            $job->status = 'failed';
            $job->error = "{$error} in {$file} on line {$line}";
            $job->trace = $trace;
            $io->error("Failed job {$title}: {$error} @ {$file}:{$line}");
          }
        }

        $jobsTable->save($job);
      } else {
        if($once){
          break;
        }
        // Show animated "Working..." while idle
        $io->out("No jobs found. Sleeping...");
        // $io->overwrite("No jobs found. Sleeping" . $frames[$frameIndex], 0);
        // $frameIndex = ($frameIndex + 1) % count($frames);
        sleep(1);
      }
    }

    $io->out("Worker shut down cleanly.");
    return Command::CODE_SUCCESS;
  }
}
