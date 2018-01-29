<?php

namespace FilippoToso\LaravelSupervisor;

use Illuminate\Console\Command;

class RunSupervisor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'supervisor:run {--name=default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run supervisor to start the queue from crontab';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $name = $this->option('name');

        if ($name == 'default') {
            $params = config('supervisor.default');
        } else {
            $params = config('supervisor.queues.' . $name);
        }

        if (is_null($params)) {
            $this->error("I can't find the {$name} parameters in the configuration!");
        }

        $folder = str_finish(config('supervisor.folder'), '/');

        if (!is_dir($folder)) {
            $this->error("The {$folder} directory doesn't exist!");
        }

        $file = $folder . $name . '.lock';

        $handle = fopen($file, 'w+');

        if (flock($handle, LOCK_EX | LOCK_NB)) {
            Artisan::call('queue:work', $params);
        }

        fclose($handle);

    }

}
