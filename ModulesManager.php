<?php

namespace Vitlabs\Modules;

use File;
use Vitlabs\ModulesContracts\Contracts\ModulesManagerContract;
use Vitlabs\ModulesContracts\Contracts\ModuleContract;

class ModulesManager implements ModulesManagerContract {

    protected $repository = null;

    public function __construct()
    {
        // Get repository instance
        $this->repository = app('Vitlabs\ModulesContracts\Contracts\ModulesRepositoryContract');
    }

    // Get repository (ModulesRepositoryContract)
    public function getRepository()
    {
        return $this->repository;
    }

    // Install modules
    public function installAll()
    {
        foreach ($this->repository->uninstalled() as $module)
        {
            $this->installByInstance($module);
        }

        return $this;
    }

    public function install($moduleName)
    {
        $module = $this->repository->get($moduleName);

        if (is_null($module))
        {
            return false;
        }

        return $this->installByInstance($module);
    }

    public function installByInstance(ModuleContract $module)
    {
        $installer = $module->getInstaller();

        if ( ! is_null($installer))
        {
            call_user_func([$installer, 'install'], $module, $this);
        }

        $module->setInstalled(true);

        return $module->save();
    }

    // Uninstall modules
    public function uninstallAll()
    {
        foreach ($this->repository->installed() as $module)
        {
            $this->uninstallByInstance($module);
        }

        return $this;
    }

    public function uninstall($moduleName)
    {
        $module = $this->repository->get($moduleName);

        if (is_null($module))
        {
            return false;
        }

        return $this->uninstallByInstance($module);
    }

    public function uninstallByInstance(ModuleContract $module)
    {
        $uninstaller = $module->getUninstaller();

        if ( ! is_null($uninstaller))
        {
            call_user_func([$uninstaller, 'uninstall'], $module, $this);
        }

        $module->setInstalled(false);

        return $module->save();
    }

    // Remove modules
    public function removeAll()
    {
        foreach ($this->repository->all() as $module)
        {
            $this->removeByInstance($module);
        }

        return $this;
    }

    public function removeInstalled()
    {
        foreach ($this->repository->installed() as $module)
        {
            $this->removeByInstance($module);
        }

        return $this;
    }

    public function removeUninstalled()
    {
        foreach ($this->repository->uninstalled() as $module)
        {
            $this->removeByInstance($module);
        }

        return $this;
    }

    public function remove($moduleName)
    {
        $module = $this->repository->get($moduleName);

        if (is_null($module))
        {
            return false;
        }

        return $this->removeByInstance($module);
    }

    public function removeByInstance(ModuleContract $module)
    {
        // First, uninstall the module
        $this->uninstallByInstance($module);

        // Remove module directory
        File::deleteDirectory($module->getPath());

        return true;
    }

    /* Repository methods */

    // Module directories
    public function addDirs($dirs = [])
    {
        return $this->repository->addDirs($dirs = []);
    }

    public function addDir($dir)
    {
        return $this->repository->addDir($dir);
    }

    public function hasDir($dir)
    {
        return $this->repository->hasDir($dir);
    }

    public function removeDir($dir)
    {
        return $this->repository->removeDir($dir);
    }

    public function getDirs()
    {
        return $this->repository->getDirs();
    }

    /**
     * Find all modules in set directories.
     * @return array of Vitlabs\ModulesContracts\Contracts\ModuleContract
     */
    public function all()
    {
        return $this->repository->all();
    }

    public function get($moduleName)
    {
        return $this->repository->get($moduleName);
    }

    public function installed()
    {
        return $this->repository->installed();
    }

    public function uninstalled()
    {
        return $this->repository->uninstalled();
    }

    public function has($module)
    {
        return $this->repository->has($module);
    }

    /**
     * Reload and recache modules.
     * @return $this
     */
    public function reload()
    {
        return $this->repository->reload();
    }

}