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

    protected $lockFiles = [];

    public function __construct()
    {
        parent::__construct();

        $this->setup();
    }

    protected function setup()
    {
        $this->folder = $this->folder();

        register_shutdown_function([$this, 'shutdown']);
        pcntl_signal(SIGINT,  [$this, 'shutdown']);
        pcntl_signal(SIGTERM, [$this, 'shutdown']);
        pcntl_signal(SIGHUP,  [$this, 'shutdown']);
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

            if (file_exists($file)) {
                $this->log('Command %s already running', $name);
                break;
            }

            $this->log('Executing command %s', $name);
            Artisan::call($command, $params);

            $this->createLockFile($file);
        }

        if (file_exists($this->stopFile())) {
            unlink($this->stopFile());
        }
    }

    protected function createLockFile($file)
    {
        touch($file);

        $this->lockFiles[] = $file;
    }

    /**
     * Destruct the class
     */
    public function __destruct()
    {
        $this->shutdown();
    }

    /**
     * Shutdown function used to cleanup. Do not call directly
     *
     * @return void
     */
    public function shutdown()
    {
        foreach ($this->lockFiles as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
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
