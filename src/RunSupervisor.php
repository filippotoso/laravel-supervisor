<?php

namespace FilippoToso\LaravelSupervisor;

use FilippoToso\LaravelSupervisor\Traits\Supervisorable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class RunSupervisor extends Command
{
    use Supervisorable;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'supervisor:run {--debug}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run supervisor to start the configured long lived Artisan commands';

    /**
     * The folder where the lock files will be stored.
     *
     * @var string
     */
    protected $folder;

    public function __construct()
    {
        parent::__construct();

        $this->folder = $this->folder();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->prepare();

        $commands = config('supervisor.commands', []);

        foreach ($commands as $name => $command) {
            $this->do($name, $command['command'], $command['params'] ?? []);
        }
    }

    protected function do($name, $command, $params)
    {
        $this->log('Ready to run the %s command (%s)', $name, $this->commandToString($command, $params));

        while (!file_exists($this->stopFile())) {
            $file = $this->folder . $name . '.lock';

            $handle = fopen($file, 'w+');

            if (flock($handle, LOCK_EX | LOCK_NB)) {
                $this->log('Executing command %s', $name);
                Artisan::call($command, $params);
            } else {
                $this->log('Command %s already running', $name);
                break;
            }

            fclose($handle);
        }

        if (file_exists($this->stopFile())) {
            unlink($this->stopFile());
        }
    }

    protected function commandToString($command, $params)
    {
        $result = $command;

        foreach ($params as $key => $value) {
            $result .= ' ' . (is_int($key) ? $value : $key . '=' . $value);
        }

        return trim($result);
    }
}
