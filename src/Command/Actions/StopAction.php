<?php

declare(strict_types=1);

namespace QueueWorker\Command\Actions;

use Cake\Command\Command;
use Cake\Console\ConsoleIo;

class StopAction
{
  private const STOP_FILE = TMP . 'cake_queue_stop.flag';

  public function run(ConsoleIo $io): int
  {
    file_put_contents(self::STOP_FILE, 'stop');
    $io->out('🛑 Stop flag written. Worker will shut down shortly.');
    return Command::CODE_SUCCESS;
  }
}
