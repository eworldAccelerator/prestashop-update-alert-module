<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author    eworld Accelerator
 * @copyright 2010-2016 E-WORLD-CONCEPT SAS
 * @license   LICENSE.txt
 */
class UpdateAlertModule
{
    private $name;
    private $moduleName;
    private $currentVersion;
    private $availableVersion;

    public function __construct($name, $moduleName, $currentVersion, $availableVersion)
    {
        $this->name = $name;
        $this->moduleName = $moduleName;
        $this->currentVersion = $currentVersion;
        $this->availableVersion = $availableVersion;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getModuleName()
    {
        return $this->moduleName;
    }

    /**
     * @return mixed
     */
    public function getCurrentVersion()
    {
        return $this->currentVersion;
    }

    /**
     * @return mixed
     */
    public function getAvailableVersion()
    {
        return $this->availableVersion;
    }
}