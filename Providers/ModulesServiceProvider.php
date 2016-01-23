<?php

namespace Vitlabs\Modules\Providers;

use Exception;
use Modules;
use Illuminate\Support\ServiceProvider;

class ModulesServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Add dirs wwhre to find modules from config
        foreach (config('modules.dirs') as $dir)
        {
            Modules::addDir(base_path($dir));
        }

        // Boot installed modules
        foreach (Modules::installed() as $module)
        {
            // Register module service providers
            $providers = $module->getProviders();

            if (is_array($providers))
            {
                foreach($providers as $provider)
                {
                    $this->app->register($provider);
                }
            }

            // Require files (relative to the module route path)
            $files = $module->getFiles();

            if (is_array($files))
            {
                foreach ($files as $file)
                {
                    require_once $module->getPath() . '/' . $file;
                }
            }
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Publish configuration file
        $this->publishes([
            __DIR__ . '/../Config/modules.php' => config_path('modules.php'),
        ], 'config');

        // Merge configuration file
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/modules.php', 'modules'
        );

        // Bind ModulesRepositoryContract implementation
        $this->app->bind('Vitlabs\ModulesContracts\Contracts\ModulesRepositoryContract', 'Vitlabs\Modules\NativeModulesRepository');

        // Bind ModulesManagerContract implementation
        $this->app->bind('Vitlabs\ModulesContracts\Contracts\ModulesManagerContract', 'Vitlabs\Modules\ModulesManager', true);

        // Bind ModuleContract implementation
        $this->app->bind('Vitlabs\ModulesContracts\Contracts\ModuleContract', 'Vitlabs\Modules\Module');

        // Bind ModuleFactoryContract implementation
        $this->app->bind('Vitlabs\ModulesContracts\Contracts\ModuleFactoryContract', 'Vitlabs\Modules\ModuleFactory');
    }
}
