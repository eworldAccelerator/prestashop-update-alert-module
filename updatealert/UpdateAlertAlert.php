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
class UpdateAlertAlert {
	/** @var int $firstAlert */
	private $firstAlert;
	/** @var int $lastAlert */
	private $lastAlert;
	/** @var string $moduleName */
	private $moduleName;
	/** @var string $version */
	private $version;

	public function __construct($moduleName, $version, $firstAlert) {
		$this->firstAlert = $firstAlert;
		$this->lastAlert = $firstAlert;
		$this->moduleName = $moduleName;
		$this->version = $version;
	}

	/**
	 * @return int
	 */
	public function getFirstAlert() {
		return $this->firstAlert;
	}

	/**
	 * @return int
	 */
	public function getLastAlert() {
		return $this->lastAlert;
	}

	/**
	 * @return string
	 */
	public function getModuleName() {
		return $this->moduleName;
	}

	/**
	 * @return string
	 */
	public function getVersion() {
		return $this->version;
	}

	public function alertSent() {
		$this->lastAlert = time();
	}
}