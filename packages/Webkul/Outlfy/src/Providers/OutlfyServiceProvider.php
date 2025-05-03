<?php

namespace Webkul\Outlfy\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Webkul\Admin\Http\Controllers\Lead\LeadController as BaseLeadController;
use Webkul\Outlfy\Http\Controllers\Lead\LeadController;

class OutlfyServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');

        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'outlfy');

        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'outlfy');

        Event::listen('admin.layout.head.after', function ($viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('outlfy::components.layouts.style');
        });

        $this->registerControllers();
        $this->publishAssets();
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConfig();
    }

    /**
     * Register package config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/menu.php',
            'menu.admin'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/acl.php',
            'acl'
        );
    }

    /**
     * Publish the view files for overriding.
     */
    protected function publishAssets(): void
    {
        $this->publishes([
            __DIR__ . '/../Resources/views/settings/users/index.blade.php' =>
            resource_path('views/vendor/admin/settings/users/index.blade.php'),
            __DIR__ . '/../Resources/views/leads/create.blade.php' =>
            resource_path('views/vendor/admin/leads/create.blade.php'),
        ]);
    }

    /**
     * Bind the custom controllers.
     */
    protected function registerControllers()
    {
        $this->app->bind(
            BaseLeadController::class,
            LeadController::class,
        );
    }
}
