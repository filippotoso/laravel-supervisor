<?php

namespace FilippoToso\LaravelSupervisor;

use FilippoToso\LaravelSupervisor\Traits\Supervisorable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class StopSupervisor extends Command
{
    use Supervisorable;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'supervisor:stop {--debug}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tell supervisor to quit after the command is completed';

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

        touch($this->stopFile());

        $this->log('Supervisor has been notified to quit after the commands completed their execution');
    }
}
