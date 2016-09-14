<?php
/**
 * Created by PhpStorm.
 * User: Ben
 * Date: 14/09/2016
 * Time: 15:07
 */

class UpdateAlertAlert {
	private $firstAlert;
	private $lastAlert;
	private $moduleName;
	private $version;

	function __construct($moduleName, $version, $firstAlert) {
		$this->firstAlert = $firstAlert;
		$this->lastAlert = $firstAlert;
		$this->moduleName = $moduleName;
		$this->version = $version;
	}

	/**
	 * @return mixed
	 */
	public function getFirstAlert() {
		return $this->firstAlert;
	}

	/**
	 * @return mixed
	 */
	public function getLastAlert() {
		return $this->lastAlert;
	}

	/**
	 * @return mixed
	 */
	public function getModuleName() {
		return $this->moduleName;
	}

	/**
	 * @return mixed
	 */
	public function getVersion() {
		return $this->version;
	}

	public function alertSent() {
		$this->lastAlert = time();
	}
}