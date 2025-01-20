<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PreCommitChecks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dev:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs prep and checks before a commit is made';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Running pre-commit checks and prep");

        $this->newLine(2);
        $this->info("========== Running ide-helper:actions ==========");
        $this->call('ide-helper:actions');


        $this->newLine(2);
        $this->info("========== Running phpstan ==========");
        // run php or sail php ./vendor/bin/phpstan analyse from command line
        try {
            $process = new \Symfony\Component\Process\Process(['./vendor/bin/sail', 'php', './vendor/bin/phpstan', 'analyse']);
            $process->run(function ($type, $buffer) {
                if (\Symfony\Component\Process\Process::ERR === $type) {
                    $this->output->write($buffer);
                    throw new \Exception("Failed to run phpstan via sail");
                } else {
                    $this->output->write($buffer);
                }
            });
        } catch (\Exception $e) {
            $this->comment("Trying to run phpstan directly with php");

            try {
                $process = new \Symfony\Component\Process\Process(['php', './vendor/bin/phpstan', 'analyse']);
                $process->run(function ($type, $buffer) {
                    if (\Symfony\Component\Process\Process::ERR === $type) {
                        $this->output->write($buffer);
                    } else {
                        $this->output->write($buffer);
                    }
                });
            } catch (\Exception $e) {
                $this->error("Failed to run phpstan: " . $e->getMessage());
            }
        }

        $this->newLine(2);
        $this->info("========== Completed pre-commit checks and prep ==========");
        $this->comment("Please check messages above for any errors or warnings");
    }
}
