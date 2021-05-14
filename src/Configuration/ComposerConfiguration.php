<?php

namespace Ecoflow\Core\Configuration;

use Illuminate\Support\Facades\File;

class ComposerConfiguration
{
    /**
     * Array of all composer packages.
     * 
     * @var array
     */
    public array $packages;

    /**
     * Array of all pacakges without versions.
     * 
     * @var array
     */
    public array $packagesWithoutVersion;

    /**
     * Array of ecoflow Package (with versions).
     * 
     * @var array
     */
    public array $ecoflowPackages;

    /**
     * Array of Ecoflow pacakges without versions
     * 
     * @var array
     */
    public array $ecoflowPackagesWithoutVersion;

    /**
     * Array of ecoflow package names.
     * 
     * @var array
     */
    public array $ecoflowPackageName;

    /**
     * Make a new ComposerConfiguration
     * 
     */
    public function __construct()
    {
        $this->packages = $this->getPackages();
        $this->packagesWithoutVersion = $this->getPackageswithoutVersion($this->packages);
        $this->ecoflowPackages = $this->getEcoflowPackages($this->packages);
        $this->ecoflowPackagesWithoutVersion = $this->getEcoflowPackagesWithoutVersion($this->packagesWithoutVersion);
        $this->ecoflowPackageName = $this->getEcoflowPackagesName($this->ecoflowPackagesWithoutVersion);
    }

    /**
     * Return all required packages in packages.json file
     *
     * @return array
     */
    public function getPackages(): array
    {
        return get_object_vars(json_decode(File::get('composer.json'))->require);
    }

    /**
     * Return all required packages in packages.json file
     *
     * @return array
     */
    public function getPackageswithoutVersion($packages): array
    {
        return array_keys($packages);
    }

    /**
     * Get EcoFlow packages with vendor ecoflow/<pkg-name> => version.
     *
     * @return array
     */
    public function getEcoflowPackages(array $packages): array
    {
        return array_filter($packages, function ($version, $package) {
            return explode('/', $package)[0] === "ecoflow";
        }, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * Get EcoFlow packages without version number.
     *
     * @param array $pkgs
     * @return void
     */
    public function getEcoflowPackagesWithoutVersion(array $packages): array
    {
        return array_filter($packages, function ($package) {
            return explode('/', $package)[0] === "ecoflow";
        });
    }

    /**
     * Get Ecoflow packages names without the vendor.
     *
     * @return array
     */
    public function getEcoflowPackagesName(array $packages): array
    {
        return array_map(function ($pack) {
            return explode('/', $pack)[1];
        }, $packages);
    }
}
