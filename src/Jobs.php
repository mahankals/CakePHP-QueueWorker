<?php

declare(strict_types=1);

namespace QueueWorker;

use Cake\ORM\Locator\LocatorAwareTrait;

class Jobs
{
  use LocatorAwareTrait;

  public function create(string $jobClass, array $data = [], ?string $title = null): bool
  {
    $jobs = $this->getTableLocator()->get('QueueWorker.QueueJobs');
    if (!method_exists($jobs, 'createJob')) {
      throw new \RuntimeException('QueueJobsTable does not implement createJob()');
    }

    $job = (new \ReflectionClass($jobClass))->getShortName();
    $job = preg_replace('/Job$/', '', $job); // Remove 'Job' suffix if needed

    return (bool)$jobs->createJob($job, $data, $title);
  }
}
