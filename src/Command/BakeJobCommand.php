<?php

declare(strict_types=1);

namespace QueueWorker\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

class BakeJobCommand extends Command
{
  private const command = 'bake job';

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
    return 'Create Sample Job';
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
    $parser
      ->addArgument('name', [
        'help' => 'The name of the job to bake',
        'required' => true,
      ])
      ->addOption('force', [
        'short' => 'f',
        'help' => 'Overwrite the job file if it already exists',
        'boolean' => true,
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
    $rawName  = $args->getArgument('name');
    $force = $args->getOption('force');

    // Normalize to PascalCase
    $classBase = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $rawName)));
    $className = $classBase . 'Job';
    $fileName = $className . '.php';

    $file = ROOT . DS . 'src' . DS . 'Jobs' . DS . $fileName;

    if (file_exists($file) && !$force) {
      $answer = strtolower(trim($io->ask("File `{$file}` exists. \n Do you want to overwrite? (y/n)", 'n')));

      if (!in_array($answer, ['y', 'yes'], true)) {
        $io->out("Skip `{$file}`");
        return Command::CODE_SUCCESS;
      }
    }

    if (!is_dir(dirname($file))) {
      mkdir(dirname($file), 0775, true);
    }

    // Job class content
    $content = <<<PHP
<?php
declare(strict_types=1);

namespace App\Jobs;

use QueueWorker\Jobs\BaseJob;

class {$className} extends BaseJob
{
    public function run(array \$data): void
    {
        // Your logic here
        echo "Running job {$rawName} with data: " . json_encode(\$data) . PHP_EOL;
        sleep(10);
        echo "Completed job {$rawName} with data: " . json_encode(\$data) . PHP_EOL;
    }
}
PHP;

    file_put_contents($file, $content);
    $io->out("Wrote {$file}");
  }
}
