<?php

namespace Vitlabs\Modules;

use File;
use ErrorException;
use Vitlabs\ModulesContracts\Contracts\ModuleContract;

class Module implements ModuleContract {

    protected $path;
    protected $name;
    protected $installed;
    protected $protected;
    protected $providers;
    protected $files;
    protected $installer;
    protected $uninstaller;

    // Main methods
    public function __construct($path, $name, $installed = false, $protected = false, array $providers = [], array $files = [], $installer = null, $uninstaller = null)
    {
        $this->setPath($path);
        $this->setName($name);
        $this->setInstalled($installed);
        $this->setProtected($protected);
        $this->setProviders($providers);
        $this->setFiles($files);
        $this->setInstaller($installer);
        $this->setUninstaller($uninstaller);
    }

    public function getPath()
    {
        return $this->path;
    }

    protected function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    // Name
    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    // Installed
    public function isInstalled()
    {
        return $this->installed;
    }

    public function setInstalled($installed)
    {
        $this->installed = boolval($installed);

        return $this;
    }

    // Protected
    public function isProtected()
    {
        return $this->protected;
    }

    public function setProtected($protected)
    {
         $this->protected = boolval($protected);

        return $this;
    }

    // Providers
    public function getProviders()
    {
        return $this->providers;
    }

    public function setProviders(array $providers)
    {
        $this->providers = $providers;

        return $this;
    }

    // Files
    public function getFiles()
    {
        return $this->files;
    }

    public function setFiles(array $files)
    {
        $this->files = $files;

        return $this;
    }

    // Installer
    public function getInstaller()
    {
        return $this->installer;
    }

    public function setInstaller($installer)
    {
        $this->installer = $installer;

        return $this;
    }

    // Uninstaller
    public function getUninstaller()
    {
        return $this->uninstaller;
    }

    public function setUninstaller($uninstaller)
    {
        $this->uninstaller = $uninstaller;

        return $this;
    }

    // Other
    public function save()
    {
        // Make json content
        $json = [
            'name' => $this->name,
            'installed' => $this->installed,
            'protected' => $this->protected,
            'providers' => $this->providers,
            'files' => $this->files,
            'installer' => $this->installer,
            'uninstaller' => $this->uninstaller,
        ];

        // Encode json content
        $encodedJson = json_encode($json, JSON_PRETTY_PRINT);

        // Save module metafile
        try
        {
            return (File::put($this->path . '/' . ModuleContract::METAFILE, $encodedJson, true) > 0);
        }
        catch (ErrorException $e)
        {
            return false;
        }
    }
}