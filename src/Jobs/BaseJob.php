<?php
namespace QueueWorker\Jobs;

abstract class BaseJob
{
  // To be implemented by extending classes
  abstract protected function run(array $data);
}
