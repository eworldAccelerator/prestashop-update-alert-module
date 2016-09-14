<?php
/**
 * Created by PhpStorm.
 * User: Ben
 * Date: 22/09/2015
 * Time: 19:22
 */
ini_set('display_errors', 1);
//require_once '../../eworldAcceleratorHandler.php';

class AdminUpdateAlertController extends ModuleAdminController {
	private $modulePath;
	private $errorList;
	private $okList;
	public function __construct() {
		$this->modulePath = _PS_ROOT_DIR_.'/modules/eworldaccelerator/';
		$this->moduleUrl = __PS_BASE_URI__.'modules/eworldaccelerator/';
		$this->bootstrap = true;
		$this->display = 'view';
		$this->errorList = array();
		$this->okList = array();
		parent::__construct();
	}
	public function initContent() {
		$this->display = 'view';
		parent::initContent();
	}
	public function renderView() {
		$eworldAcceleratorHandler = new UpdateAlertHandler(trim(Configuration::get('EWORLDACCELERATOR_DIRECTORY', '')));
		$version = $eworldAcceleratorHandler->getVersion();
		$this->context->smarty->assign(array(
			'module_dir' => $this->moduleUrl,
			'eacc_version' => $version,
			'errors' => sizeof($this->errorList) > 0 ? Tools::displayError(join('<br>', $this->errorList)).'<br>' : '',
			'oks' => sizeof($this->okList) > 0 ? ModuleCore::displayConfirmation(join('<br>', $this->okList)).'<br>' : '',
			'eworldAcceleratorHandler' => $eworldAcceleratorHandler,
		));
		$html = $output = $this->context->smarty->fetch($this->modulePath.'views/templates/admin/view.tpl');

		return $html;
	}

	/**
	 * Save form data.
	 */
	public function postProcess() {
		if (Tools::isSubmit('submitAction') == 1) {
			$action = trim(Tools::getValue('action'));
			$eworldAcceleratorHandler = new UpdateAlertHandler(trim(Configuration::get('EWORLDACCELERATOR_DIRECTORY', '')));
			switch ($action) {
				case 'deleteAllCache' :
					$eworldAcceleratorHandler->deleteAllCache();
					$this->okList[] = $this->l('All cache files have been deleted.');
					break;
				case 'cdnPurge' :
					$eworldAcceleratorHandler->purgeCDN();
					$this->okList[] = $this->l('Purge tasks have been sent to CDN servers. It will take several minutes.');
					break;
				case 'gc' :
					$eworldAcceleratorHandler->garbageCollector();
					$this->okList[] = $this->l('All expired cache files have been deleted.');
					break;
				default :
					$this->errorList[] = Tools::displayError($this->l('Unknow action asked'));
			}
		}
	}
}