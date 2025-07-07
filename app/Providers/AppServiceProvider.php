<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\SessionService;
use Illuminate\Support\Facades\Config;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SessionService::class, function ($app) {
            return new SessionService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $hostname = gethostname();

    if ($hostname !== 'DESKTOP-LIB0CRC') {
        Config::set('database.connections.mysql.database', env('DB_DATABASE_PROD'));
        Config::set('database.connections.mysql.username', env('DB_USERNAME_PROD'));
        Config::set('database.connections.mysql.password', env('DB_PASSWORD_PROD'));
    } else {
        Config::set('database.connections.mysql.database', env('DB_DATABASE_DEV'));
        Config::set('database.connections.mysql.username', env('DB_USERNAME_DEV'));
        Config::set('database.connections.mysql.password', env('DB_PASSWORD_DEV'));
    }
    }
}
