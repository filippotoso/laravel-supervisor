<?php

namespace FilippoToso\LaravelSupervisor\Traits;

trait Supervisorable
{
    protected function folder()
    {
        return str_finish(config('supervisor.folder'), DIRECTORY_SEPARATOR);
    }

    protected function stopFile()
    {
        return $this->folder() . 'supervisor.stop';
    }

    protected function log($message, ...$args)
    {
        if ($this->option('debug')) {
            $this->info(sprintf($message, ...$args));
        }
    }

    protected function prepare()
    {
        $this->log('Preparing folder %s', $this->folder);

        if (!is_dir($this->folder)) {
            mkdir($this->folder, 0777, true);
        }

        if (!is_dir($this->folder)) {
            $this->error("The {$this->folder} directory doesn't exist!");
            return FALSE;
        }
    }
}
