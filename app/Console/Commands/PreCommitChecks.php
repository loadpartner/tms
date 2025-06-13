<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class PreCommitChecks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dev:check {--refresh-db : Refresh the testing database before running tests}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs prep and checks before a commit is made';

    protected $checks = [
        'ide-helper' => false,
        'phpstan' => false,
        'npm-lint' => false,
        'typescript' => false,
        'npm-build' => false,
        'tests:php' => false,
    ];

    protected $commands = [
        'phpstan' => 'php ./vendor/bin/phpstan analyse',
        'npm-lint' => 'npm run lint',
        'typescript' => 'npm exec tsc',
        'npm-build' => 'npm run build',
        'tests:php' => 'php ./vendor/bin/pest --parallel',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Running pre-commit checks and prep");

        $this->runIdeHelperCheck();
        
        foreach ($this->commands as $check => $command) {
            $this->newLine(2);
            $env = [];
            
            // Refresh testing database before running tests
            if ($check === 'tests:php') {
                $this->refreshTestingDatabase();
                $env = [
                    'APP_ENV' => 'testing',
                    'DB_CONNECTION' => 'sqlite',
                    'DB_DATABASE' => ':memory:',
                ];
            }
            
            $this->info("========== Running $check ==========");
            
            $successCallback = $this->getSuccessCallback($check);
            
            $this->runProcess(explode(' ', $command), $check, $successCallback, $env);
        }

        $this->displaySummary();

        return in_array(false, $this->checks) ? 1 : 0;
    }

    protected function runProcess(array $command, string $check, callable $successCallback, array $env = [], bool $tty = true): void
    {
        try {
            $process = new Process($command);
            $process->setTty($tty);
            
            if ($env) {
                // Merge with current environment to preserve existing variables
                $currentEnv = $_ENV + $_SERVER;
                $mergedEnv = array_merge($currentEnv, $env);
                $process->setEnv($mergedEnv);
            }

            $output = '';
            $process->run(function ($type, $buffer) use (&$output) {
                $this->output->write($buffer);
                $output .= $buffer;
            });

            $this->checks[$check] = $successCallback($output, $process);
        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), 'TTY') && $tty === true) {
                logger()->info("TTY error, running without TTY");
                $this->runProcess($command, $check, $successCallback, $env, false);
                return;
            }
            
            $this->error("Failed to run $check: " . $e->getMessage());
        }
    }


    protected function getSuccessCallback(string $check): callable
    {
        return match($check) {
            default => fn($output, $process) => $process->isSuccessful()
        };
    }

    protected function runIdeHelperCheck(): void
    {
        $this->newLine(2);
        $this->info("========== Running ide-helper:actions ==========");
        
        $helperPath = base_path('_ide_helper_actions.php');
        $originalHash = file_exists($helperPath) ? md5_file($helperPath) : null;
        
        $this->call('ide-helper:actions');
        
        $newHash = file_exists($helperPath) ? md5_file($helperPath) : null;
        
        if ($originalHash !== $newHash) {
            $this->error('The ide-helper:actions file has changed. Please commit the updated _ide_helper_actions.php file.');
            $this->checks['ide-helper'] = false;
        } else {
            $this->checks['ide-helper'] = true;
        }
    }

    protected function displaySummary(): void
    {
        $this->newLine(2);
        $this->info("========== Completed pre-commit checks and prep ==========");
        
        $this->newLine();
        $this->info("Summary Report:");
        foreach ($this->checks as $check => $passed) {
            $icon = $passed ? '<fg=green>✓</>' : '<fg=red>✗</>';
            $this->line("$icon $check");
        }
        
        $this->newLine();
        $this->comment("Please check messages above for any errors or warnings");
    }

    protected function refreshTestingDatabase(): void
    {
        $this->info("Refreshing testing database...");
        
        try {
            // Override the database configuration for testing
            config([
                'database.connections.sqlite.database' => ':memory:',
                'database.default' => 'sqlite',
            ]);
            
            $this->call('migrate:fresh', [
                '--env' => 'testing',
                '--seed' => true,
                '--database' => 'sqlite',
            ]);
            $this->info("Testing database refreshed successfully");
        } catch (\Exception $e) {
            $this->error("Failed to refresh testing database: " . $e->getMessage());
        }
    }
}
