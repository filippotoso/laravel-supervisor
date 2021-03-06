<?php

namespace FilippoToso\LaravelSupervisor;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class SupervisorServiceProvider extends ServiceProvider
{

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        $this->publishes([
            dirname(__DIR__) . '/config/supervisor.php' => config_path('supervisor.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([RunSupervisor::class]);
            $this->commands([StopSupervisor::class]);
        }
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/config/default.php',
            'supervisor'
        );
    }
}
