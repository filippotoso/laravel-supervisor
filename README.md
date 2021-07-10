# Laravel Supervisor

A simple supervisor tool that allows to run the Laravel Queue from the crontab.

## Requirements

- PHP 5.6+
- Laravel 5.0+

## Installing

Use Composer to install it:

```
composer require filippo-toso/laravel-supervisor
```

## Using It

For a small site you just need to add the following command to your crontab (once every minute).

```
php artisan supervisor:run
```

For larger sites you may want to publish the configuration and add multiple commands to run different queues:

```
php artisan vendor:publish --provider="FilippoToso\LaravelSupervisor\SupervisorServiceProvider"
```

In the configuration file you can find useful details.

## Limitations

This package starts a command at each execution. This means that, if you have configured 5 commands to be executed, it can take up to 5 minutes to start them all.
It also means that's best to place the most important commands (i.e. the queue) at the beginning of the commnads array.

If this limitation is a dealbreaker for you, just use the real [supervisor](http://supervisord.org/)!

