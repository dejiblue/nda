<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     * 
     * @return void
     */
    public function boot()
    {
        //
        $this->commands([
            \App\Console\Commands\ImportContacts::class
        ]);
        // Use scheduler to execute this command every minute
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->command('import:contacts')->everyMinute();
        });
    }
}
