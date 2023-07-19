<?php

namespace Ascentech\MassiveCsvImport;

use Illuminate\Support\ServiceProvider;

class MassiveCsvImportServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot() // route
    {
        //include __DIR__.'/routes.php';
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'massive-csv-import');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'massive-csv-import');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/routes.php');

        $this->publishes([
                __DIR__.'/../config/massive-csv-import.php' => config_path('massive-csv-import.php'),
            ], 'config');

        if ($this->app->runningInConsole()) {
            // $this->publishes([
            //     __DIR__.'/../config/config.php' => config_path('massive-csv-import.php'),
            // ], 'config');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/massive-csv-import'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/massive-csv-import'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/massive-csv-import'),
            ], 'lang');*/

            // Registering package commands.
            // $this->commands([]);
        }
    }

    /**
     * Register the application services.
     */
    public function register() // bind classes
    {        
        // // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/massive-csv-import.php', 'massive-csv-import');        

        // Register the main class to use with the facade
        $this->app->singleton('massive-csv-import', function () {
            return new MassiveCsvImport;
        });
    }
}
