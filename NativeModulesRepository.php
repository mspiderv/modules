<?php

namespace Vitlabs\Modules;

use Vitlabs\ModulesContracts\Contracts\ModuleContract;
use Vitlabs\ModulesContracts\Contracts\ModulesRepositoryContract;

class NativeModulesRepository implements ModulesRepositoryContract {

    protected $dirs = [];
    protected $modules = null;
    protected $installedModules = null;
    protected $uninstalledModules = null;
    protected $moduleFactory = null;

    public function __construct()
    {
        $this->moduleFactory = app('Vitlabs\ModulesContracts\Contracts\ModuleFactoryContract');
    }

    // Module directories
    public function addDirs($dirs = [])
    {
        // Add dirs
        foreach ($dirs as $dir)
        {
            $this->addDir($dir);
        }

        return $this;
    }

    public function addDir($dir)
    {
        // If we already got the dir, continue
        if ( ! $this->hasDir($dir))
        {
            // Add directory
            $this->dirs[] = $dir;
        }

        return $this;
    }

    public function hasDir($dir)
    {
        return array_search($dir, $this->dirs) !== false;
    }

    public function removeDir($dir)
    {
        if (($key = array_search($dir, $this->dirs)) !== false) {
            unset($this->dirs[$key]);
        }

        return $this;
    }

    public function getDirs()
    {
        return $this->dirs;
    }

    /**
     * Find all modules in set directories.
     * @return array of Vitlabs\ModulesContracts\Contracts\ModuleContract
     */
    public function all()
    {
        $this->loadIfNeed();

        return $this->modules;
    }

    public function get($moduleName)
    {
        $this->loadIfNeed();

        return (isset($this->modules[$moduleName])) ? $this->modules[$moduleName] : null;
    }

    public function installed()
    {
        $this->loadIfNeed();

        return $this->installedModules;
    }

    public function uninstalled()
    {
        $this->loadIfNeed();

        return $this->uninstalledModules;
    }

    public function has($module)
    {
        $this->loadIfNeed();

        return (isset($this->modules[$moduleName]));
    }

    /**
     * Reload and recache modules.
     * @return $this
     */
    public function reload()
    {
        // Clear arrays
        $this->modules = [];
        $this->installedModules = [];
        $this->uninstalledModules = [];

        // Find all modules
        $found = [];

        foreach ($this->dirs as $dir)
        {
            $found = array_merge($found, glob($dir . '/' . ModuleContract::METAFILE));
        }

        // Create modules
        foreach ($found as $pathToJson)
        {
            // Create module
            $module = $this->moduleFactory->createModule($pathToJson);

            // Module created successfuly ?
            if ($module != null)
            {
                // Get module name
                $moduleName = $module->getName();

                // Do we already have this module with the same name ?
                if (isset($this->modules[$moduleName]))
                {
                    // TODO: error
                    throw new \Exception("Module [$modulleName] already exists.");
                    continue;
                }

                // Add module to arrays
                $this->modules[$moduleName] = $module;

                if ($module->isInstalled())
                {
                    $this->installedModules[$moduleName] = $module;
                }
                else
                {
                    $this->uninstalledModules[$moduleName] = $module;
                }
            }
        }
    }

    protected function loadIfNeed()
    {
        if (is_null($this->modules))
        {
            $this->reload();
        }
    }

}