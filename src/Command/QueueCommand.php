<?php

declare(strict_types=1);

namespace QueueWorker\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use QueueWorker\Command\Actions\StopAction;
use QueueWorker\Command\Actions\RetryAction;
use QueueWorker\Command\Actions\StartAction;

/**
 * Queue command.
 */
class QueueCommand extends Command
{

  private const command = 'queue';

  /**
   * The name of this command.
   *
   * @var string
   */
  protected string $name = self::command;

  /**
   * Get the default command name.
   *
   * @return string
   */
  public static function defaultName(): string
  {
    return self::command;
  }

  /**
   * Get the command description.
   *
   * @return string
   */
  public static function getDescription(): string
  {
    return 'Manage the job queue (start, stop, retry)';
  }

  /**
   * Hook method for defining this command's option parser.
   *
   * @see https://book.cakephp.org/5/en/console-commands/commands.html#defining-arguments-and-options
   * @param \Cake\Console\ConsoleOptionParser $parser The parser to be defined
   * @return \Cake\Console\ConsoleOptionParser The built parser.
   */
  public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
  {
    $parser->addArgument('action', [
      'help' => 'Action to perform.',
      'choices' => ['start', 'retry', 'stop'],
      'required' => true,
    ]);
    $parser->addArgument('id', [
      'help' => 'Optional parameter for the action (e.g., job ID)',
      'required' => false,
    ]);
    $parser->addOption('once', [
      'short' => 'o',
      'help' => 'Start workers then exit on complition.',
      'boolean' => true,
      'default' => false,
    ]);
    return parent::buildOptionParser($parser)
      ->setDescription(static::getDescription());
  }

  /**
   * Implement this method with your command's logic.
   *
   * @param \Cake\Console\Arguments $args The command arguments.
   * @param \Cake\Console\ConsoleIo $io The console io
   * @return int|null|void The exit code or null for success
   */
  public function execute(Arguments $args, ConsoleIo $io)
  {
    $action = $args->getArgument('action');
    $id  = $args->getArgument('id');
    $once = $args->getOption('once');

    return match ($action) {
      'start' => (new StartAction())->run($io, $once),
      'stop' => (new StopAction())->run($io),
      'retry' => (new RetryAction())->run($io, $id),
      default => $io->error("Unknown action: {$action}"),
    };
  }
}
