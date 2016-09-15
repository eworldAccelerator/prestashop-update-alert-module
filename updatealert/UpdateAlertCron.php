<?php
/**
 * Created by PhpStorm.
 * User: Ben
 * Date: 14/09/2016
 * Time: 14:56
 */

class UpdateAlertCron {
	/** @var array $prestashopUpgrade */
	private $prestashopUpgrade;
	/** @var array $installedModulesList */
	private $installedModulesList;
	/** @var UpdateAlertModule[] $outdatedModulesList */
	private $outdatedModulesList;
	/** @var UpdateAlertModule[] $modulesToSendList */
	private $modulesToSendList;
	/** @var UpdateAlertAlert[] $lastAlertsList */
	private $lastAlertsList;
	/** @var string[] $recipientsList */
	private $recipientsList;
	/** @var int $lastEmailAlert */
	private $lastEmailAlert;
	/** @var int $alertDelay */
	private $alertDelay;
	/** @var bool $debugMode */
	private $debugMode;

	function __construct($debugMode=false) {
		// Init
		$this->debugMode = $debugMode;
		$this->prestashopUpgrade = array();
		$this->installedModulesList = array();
		$this->outdatedModulesList = array();
		$this->lastAlertsList = array();
		$this->modulesToSendList = array();
		$this->recipientsList = array();
		$this->lastEmailAlert = Configuration::get('EWORLDACCELERATOR_UPDATEALERT_LAST');
		$this->alertDelay = Configuration::get('EWORLDACCELERATOR_UPDATEALERT_DAYS');

		if ($this->debugMode) {
			echo 'Last email alert : '.date('Y-m-d H:i.s', $this->lastEmailAlert).'<br />';
			echo 'Alert delay : '.$this->alertDelay.' days<br />';
		}

		// Check Prestashop Core
		$upgradeCore = new UpgraderCore(true);
		$this->prestashopUpgrade = $upgradeCore->checkPSVersion();

		if ($this->debugMode && $this->isPrestashopNeedsUpgrade()) {
			echo 'Prestashop needs an upgrade to '.$this->prestashopUpgrade['name'].'<br />';
		}

		// Check modules
		$moduleList = Module::getModulesOnDisk();
		if (is_array($moduleList) && sizeof($moduleList) > 0) {
			foreach ($moduleList as $currentModule) {
				if ($currentModule->installed) {
					$this->installedModulesList[$currentModule->name] = $currentModule;
					if (!empty($currentModule->version_addons)) {
						$next_version = end($currentModule->version_addons);
						$this->outdatedModulesList[] = new UpdateAlertModule(
							$currentModule->displayName,
							$currentModule->name,
							$currentModule->database_version,
							$next_version
						);
					}
				}
			}
		}

		if ($this->debugMode) {
			echo 'Modules installed : '.sizeof($this->installedModulesList).'<br />';
			echo 'Modules outdated : '.sizeof($this->outdatedModulesList).'<br />';
		}

		// Get old alerts
		$data = Configuration::get('EWORLDACCELERATOR_UPDATEALERT_CONTENT');
		if (!empty($data)) {
			$this->lastAlertsList = unserialize($data);
		}
		if ($this->debugMode) {
			if (is_array($this->lastAlertsList) && sizeof($this->lastAlertsList) > 0) {
				echo 'Modules already "alerted" : '.sizeof($this->lastAlertsList).'<br />';
			}
			else {
				echo 'Maybe an error in unserialize from data :<br />'.htmlentities($data).'<br />';
			}
		}

		// Get email recipients
		$emailContent = Configuration::get('EWORLDACCELERATOR_UPDATEALERT_EMAIL');
		if ($emailContent != '') {
			if (strpos($emailContent, PHP_EOL) !== false) {
				$this->recipientsList = explode(PHP_EOL, $emailContent);
			}
			else {
				$this->recipientsList = array(trim($emailContent));
			}
		}
		if (sizeof($this->recipientsList) <= 0) {
			echo '$emailContent='.$emailContent.'<br />';
			echo '<h3>Recipients</h3><pre>'.print_r($this->recipientsList,1).'</pre>';
			die ('Error, recipients empty');
		}

		if ($this->debugMode) {
			echo 'Recipients : '.sizeof($this->recipientsList).'<br />';
		}

		// Check new modules update
		if (is_array($this->outdatedModulesList) && sizeof($this->outdatedModulesList) > 0) {
			foreach ($this->outdatedModulesList as $currentUpdateAlertModule) {
				$sendNew = false;
				// if none alert for this module
				if (!array_key_exists($currentUpdateAlertModule->getModuleName(), $this->lastAlertsList)) {
					$sendNew = true;
				}
				else {
					// If there is an alert, but for an older version than today
					$currentUpdateAlertAlert = $this->lastAlertsList[$currentUpdateAlertModule->getModuleName()];
					if ($currentUpdateAlertAlert->getVersion() != $currentUpdateAlertModule->getAvailableVersion()) {
						$sendNew = true;
					}
				}

				// If a new version is available for this module
				if ($sendNew) {
					if ($this->debugMode) {
						echo 'Module '.$currentUpdateAlertModule->getName().' ['.$currentUpdateAlertModule->getModuleName().'] have a new available version ('.$currentUpdateAlertModule->getAvailableVersion().')<br />';
					}
					$this->sendNewUpgradeEmail($currentUpdateAlertModule);
				}
			}
		}

		// Send global alert if needed
		if ($this->canSendEmail()) {
			// Add all outdated modules to the email list
			$this->modulesToSendList = $this->outdatedModulesList;

			// Send global email
			$this->sendEmail();

			if ($this->debugMode) {
				echo 'Global email with outdated modules ('.sizeof($this->modulesToSendList).') has been sent<br />';
			}
		}

		if ($this->debugMode) {
			echo '<h3>Recipients</h3><pre>'.print_r($this->recipientsList,1).'</pre>';
			echo '<h3>Modules installed</h3><pre>'.print_r($this->installedModulesList,1).'</pre>';
			echo '<h3>Modules outdated</h3><pre>'.print_r($this->outdatedModulesList,1).'</pre>';
			if (is_array($this->lastAlertsList) && sizeof($this->lastAlertsList) > 0) {
				echo '<h3>Modules already "alerted"</h3><pre>' . print_r($this->lastAlertsList, 1) . '</pre>';
			}
		}
	}

	/**
	 * @return array
	 */
	public function getPrestashopUpgrade() {
		return $this->prestashopUpgrade;
	}

	/**
	 * @return array
	 */
	public function getInstalledModulesList() {
		return $this->installedModulesList;
	}

	/**
	 * @return UpdateAlertModule[]
	 */
	public function getOutdatedModulesList() {
		return $this->outdatedModulesList;
	}

	/**
	 * @return UpdateAlertModule[]
	 */
	public function getModulesToSendList() {
		return $this->modulesToSendList;
	}

	/**
	 * @return UpdateAlertAlert[]
	 */
	public function getLastAlertsList() {
		return $this->lastAlertsList;
	}

	/**
	 * @return int
	 */
	public function getLastEmailAlert() {
		return $this->lastEmailAlert;
	}

	/**
	 * @return int
	 */
	public function getAlertDelay() {
		return $this->alertDelay;
	}

	/**
	 * @return bool
	 */
	public function isPrestashopNeedsUpgrade() {
		return $this->prestashopUpgrade !== false;
	}

	private function saveLastEmailSent() {
		Configuration::updateValue('EWORLDACCELERATOR_UPDATEALERT_LAST', time()-3600); // -3600 if the script takes too long
	}

	private function updateAllLastAlerts() {
		if (is_array($this->lastAlertsList) && sizeof($this->lastAlertsList) > 0) {
			foreach ($this->lastAlertsList as $key=>$value) {
				$this->lastAlertsList[$key]->alertSent();
			}
			// Save modification
			$this->saveLastAlertsList();
		}
	}

	private function saveLastAlertsList() {
		Configuration::updateValue('EWORLDACCELERATOR_UPDATEALERT_CONTENT', serialize($this->lastAlertsList));
	}

	/**
	 * @param string $moduleName
	 * @return int
	 */
	private function getModuleFirstAlert($moduleName) {
		if (is_array($this->lastAlertsList) && sizeof($this->lastAlertsList) > 0) {
			if (array_key_exists($moduleName, $this->lastAlertsList)) {
				return $this->lastAlertsList[$moduleName]->getFirstAlert();
			}
		}
		return 0;
	}

	/**
	 * @return bool
	 */
	private function canSendEmail() {
		// If elapsed time since last alert on this module exceed delay configured
		return (time() - $this->lastEmailAlert) > ($this->alertDelay * 24 * 60 * 60);
	}

	private function sendEmail() {
		global $smarty;
		if (sizeof($this->modulesToSendList) > 0) {
			$subject = '[UpdateAlert system] Your prestashop on '.$_SERVER['HTTP_HOST'].' needs upgrade';

			$htmlContent = '';
			if ($this->isPrestashopNeedsUpgrade()) {
				$htmlContent .= '<strong>Your Prestashop</strong> on '.$_SERVER['HTTP_HOST'].' needs to be upgraded to new version : <a href="'.$this->prestashopUpgrade['link'].'">'.$this->prestashopUpgrade['name'].'</a><br /><br />';
			}
			$htmlContent .= 'Module <strong>UpdateAlert</strong> on your Prestashop detects following upgrade needs :<br />
<table cellspacing="1" cellpadding="4" border="0" bgcolor="#999999">
<thead>
<tr bgcolor="#ffffff">
	<th>Module</th>
	<th>Version</th>
	<th>Upgrade to</th>
	<th>Available since</th>
</tr>
</thead>
<tbody>
';
			foreach ($this->modulesToSendList as $currentUpdateAlertModule) {
				$htmlContent .= '<tr bgcolor="#ffffff">
		<td>'.$currentUpdateAlertModule->getName().'</td>
		<td>'.$currentUpdateAlertModule->getCurrentVersion().'</td>
		<td>'.$currentUpdateAlertModule->getAvailableVersion().'</td>
		<td>'.($this->getModuleFirstAlert($currentUpdateAlertModule->getModuleName()) > 0 ? date('Y-m-d', $this->getModuleFirstAlert($currentUpdateAlertModule->getModuleName())) : '-').'</td>
	</tr>';
			}
			$htmlContent .= '</tbody>
</table>';

			// Pay attention, only text variables allowed, so HTML has been generated before
			foreach ($this->recipientsList as $currentEmail) {
				Mail::Send(
					2,
					'mail_alert',
					$subject,
					array(
						'{htmlContent}' => $htmlContent,
					),
					trim($currentEmail), NULL, NULL, NULL, NULL, NULL,
					dirname(__FILE__) . '/views/templates/'
				);
			}
			// Update last global email sent value
			$this->saveLastEmailSent();
			// Update all alerts
			$this->updateAllLastAlerts();
		}
	}

	/**
	 * @param UpdateAlertModule $updateAlertModule
	 */
	private function sendNewUpgradeEmail($updateAlertModule) {
		global $smarty;
		if (is_object($updateAlertModule)) {
			$subject = '[UpdateAlert system] A module needs update on your prestashop on '.$_SERVER['HTTP_HOST'];

			$htmlContent = 'A new upgrade is available for the module <strong>'.$updateAlertModule->getName().'</strong>.<br />
<br />
currently : '.$updateAlertModule->getCurrentVersion().'<br />
new version : '.$updateAlertModule->getAvailableVersion().'<br />';

			// Pay attention, only text variables allowed, so HTML has been generated before
			foreach ($this->recipientsList as $currentEmail) {
				Mail::Send(
					2,
					'mail_alert',
					$subject,
					array(
						'{htmlContent}' => $htmlContent,
					),
					trim($currentEmail), NULL, NULL, NULL, NULL, NULL,
					dirname(__FILE__).'/views/templates/'
				);
			}
			// Add the module to recorded alerts
			$this->lastAlertsList[$updateAlertModule->getModuleName()] = new UpdateAlertAlert(
				$updateAlertModule->getModuleName(),
				$updateAlertModule->getAvailableVersion(),
				time()
			);
			// Update lastAlertsList
			$this->saveLastAlertsList();
		}
	}
}