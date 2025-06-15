<?php

declare(strict_types=1);

namespace QueueWorker\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

class PublishCommand extends Command
{
  private const command = 'queueworker publish';

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
    return 'Publish assets';
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
    $source = dirname(__DIR__, 2) . DS . 'templates' . DS . 'Jobs' . DS . 'index.php';
    $target = ROOT . DS . 'templates' . DS . 'plugin' . DS . 'QueueWorker' . DS . 'Jobs' . DS . 'index.php';

    if (!file_exists($source)) {
      $io->err("Source template not found: {$source}");
      return static::CODE_ERROR;
    }

    if (!is_dir(dirname($target))) {
      mkdir(dirname($target), 0775, true);
    }

    if (file_exists($target)) {
      $answer = strtolower(trim($io->ask("Template already exists at: {$target}\nOverwrite? (y/n)", 'n')));
      if (!in_array($answer, ['y', 'yes'], true)) {
        $io->out("Publish cancelled.");
        return static::CODE_SUCCESS;
      }
    }

    if (copy($source, $target)) {
      $io->out("Published Templates to: {$target}");
      return static::CODE_SUCCESS;
    }

    $io->err("Failed to publish view.");
    return static::CODE_ERROR;
  }
}
