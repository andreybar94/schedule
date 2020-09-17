<?php

namespace App\Providers;

use App\Repositories\GoogleHolidaysRepository;
use App\Repositories\Interfaces\HolidaysRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(HolidaysRepository::class, GoogleHolidaysRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
