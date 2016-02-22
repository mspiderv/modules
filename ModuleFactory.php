<?php

namespace Vitlabs\Modules;

use File;
use Exception;
use Vitlabs\ModulesContracts\Contracts\ModuleFactoryContract;
use Vitlabs\ModulesContracts\Contracts\ModuleContract;

class ModuleFactory implements ModuleFactoryContract {

    public function createModule($pathToJson)
    {
        // Get JSON content
        try
        {
            $jsonContent = File::get($pathToJson);
        }
        catch (Exception $e)
        {
            return null;
        }

        // Decode JSON
        $data = json_decode($jsonContent);

        // Decoding successful ?
        if (is_null($data))
        {
            return null;
        }

        // TODO: Module metafile validation here

        // Set attributes
        $path = dirname($pathToJson);
        $name = $this->getAttribute($data, 'name', '');
        $installed = $this->getAttribute($data, 'installed', false);
        $protected = $this->getAttribute($data, 'protected', false);
        $providers = $this->getAttribute($data, 'providers', []);
        $files = $this->getAttribute($data, 'files', []);
        $installer = $this->getAttribute($data, 'installer', null);
        $uninstaller = $this->getAttribute($data, 'uninstaller', null);

        // Create module
        return app('Vitlabs\ModulesContracts\Contracts\ModuleContract', [$path, $name, $installed, $protected, $providers, $files, $installer, $uninstaller]);
    }

    protected function getAttribute($data, $key, $default = null)
    {
        return (isset($data->$key)) ? $data->$key : $default;
    }

}