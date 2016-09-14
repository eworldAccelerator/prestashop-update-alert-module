<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
	exit;
}
ini_set('display_errors', 1);
require_once 'UpdateAlertModule.php';
require_once 'UpdateAlertCron.php';
require_once 'UpdateAlertAlert.php';

class UpdateAlert extends Module {

	public function __construct() {
		$this->name = 'updatealert';
		$this->tab = 'administration';
		$this->version = '0.1.0';
		$this->author = 'eworld Accelerator';
		$this->need_instance = 0;
		$this->eworldAcceleratorHandler = null;

		/**
		 * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
		 */
		$this->bootstrap = true;

		parent::__construct();

		$this->displayName = $this->l('Update Alert');
		$this->description = $this->l('Send you emails for each update (Prestashop or modules)');

		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
	}

	/**
	 * Don't forget to create update methods if needed:
	 * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
	 */
	public function install() {
		// Install Tab
		/*$tab = new Tab();
		$tab->name[$this->context->language->id] = 'Update Alert';
		$tab->class_name = 'AdminUpdateAlert';
		$tab->id_parent = 0; // Home tab
		$tab->module = $this->name;
		$tab->add();*/

		return parent::install();
	}

	public function uninstall() {
		Configuration::deleteByName('EWORLDACCELERATOR_UPDATEALERT_ENABLED');
		Configuration::deleteByName('EWORLDACCELERATOR_UPDATEALERT_EMAILS');
		Configuration::deleteByName('EWORLDACCELERATOR_UPDATEALERT_DAYS');
		Configuration::deleteByName('EWORLDACCELERATOR_UPDATEALERT_CONTENT');
		Configuration::deleteByName('EWORLDACCELERATOR_UPDATEALERT_LAST');

		// Uninstall Tabs
		$moduleTabs = Tab::getCollectionFromModule($this->name);
		if (!empty($moduleTabs)) {
			foreach ($moduleTabs as $moduleTab) {
				$moduleTab->delete();
			}
		}
		return parent::uninstall();
	}

	/**
	 * Load the configuration form
	 */
	public function getContent() {
		/**
		 * If values have been submitted in the form, process.
		 */
		if (((bool)Tools::isSubmit('submitUpdateAlertModule')) == true) {
			$this->postProcess();
		}

		$this->context->smarty->assign('module_dir', $this->_path);

		$output = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');

		if (isset($this->errors) && is_array($this->errors) && sizeof($this->errors) > 0) {
			$output .= join("\r\n", $this->errors);
		}

		return $output . $this->renderForm();
	}

	/**
	 * Create the form that will be displayed in the configuration of your module.
	 */
	protected function renderForm() {
		$helper = new HelperForm();

		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$helper->module = $this;
		$helper->default_form_language = $this->context->language->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitUpdateAlertModule';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
			. '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');

		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id,
			'configured' => trim(Tools::getValue('EWORLDACCELERATOR_UPDATEALERT_ENABLED')) != '',
			'enabled' => trim(Tools::getValue('EWORLDACCELERATOR_UPDATEALERT_ENABLED')),
			'emailList' => trim(Tools::getValue('EWORLDACCELERATOR_UPDATEALERT_EMAIL')),
			'days' => trim(Tools::getValue('EWORLDACCELERATOR_UPDATEALERT_DAYS')),
			'updateAlertContent' => trim(Tools::getValue('EWORLDACCELERATOR_UPDATEALERT_CONTENT')) != '' ? unserialize(Tools::getValue('EWORLDACCELERATOR_UPDATEALERT_CONTENT')) : null,
		);

		return $helper->generateForm(array($this->getConfigForm()));
	}

	/**
	 * Create the structure of your form.
	 */
	protected function getConfigForm() {
		return array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Settings'),
					'icon' => 'icon-cogs',
				),
				'input' => array(
					array(
						'type'      => 'radio',
						'label'     => $this->l('Enable system'),
						'name'      => 'EWORLDACCELERATOR_UPDATEALERT_ENABLED',
						'class'     => 't',
						'required'  => true,
						'is_bool'   => true,
						'values'    => array(
							array(
								'id'    => 'active_on',                           // The content of the 'id' attribute of the <input> tag, and of the 'for' attribute for the <label> tag.
								'value' => 1,                                     // The content of the 'value' attribute of the <input> tag.
								'label' => $this->l('Enabled')                    // The <label> for this radio button.
							),
							array(
								'id'    => 'active_off',
								'value' => 2,
								'label' => $this->l('Disabled')
							)
						),
					),
					array(
						'type' => 'textarea',
						'label' => $this->l('Email list'),
						'name' => 'EWORLDACCELERATOR_UPDATEALERT_EMAIL',
						'desc' => '1 email per line',
						'required' => true
					),
					array(
						'type'     => 'text',
						'label'    => $this->l('Day(s) between alerts'),
						'name'     => 'EWORLDACCELERATOR_UPDATEALERT_DAYS',
						'class'    => 'lg',
						'required' => true,
						'desc'     => $this->l('Minimum: 1, Maximum: 31')
					),
				),
				'submit' => array(
					'title' => $this->l('Save'),
					'class' => 'btn btn-default pull-right'
				),
			),
		);
	}

	/**
	 * Set values for the inputs.
	 */
	protected function getConfigFormValues() {
		// Definied values
		if (intval(trim(Tools::getValue('EWORLDACCELERATOR_UPDATEALERT_ENABLED'))) == 1 || intval(trim(Tools::getValue('EWORLDACCELERATOR_UPDATEALERT_ENABLED'))) == 2) {
			return array(
				'EWORLDACCELERATOR_UPDATEALERT_ENABLED' => Configuration::get('EWORLDACCELERATOR_UPDATEALERT_ENABLED'),
				'EWORLDACCELERATOR_UPDATEALERT_EMAIL' => Configuration::get('EWORLDACCELERATOR_UPDATEALERT_EMAIL'),
				'EWORLDACCELERATOR_UPDATEALERT_DAYS' => Configuration::get('EWORLDACCELERATOR_UPDATEALERT_DAYS'),
			);
		}
		// Default values
		else {
			return array(
				'EWORLDACCELERATOR_UPDATEALERT_ENABLED' => 1,
				'EWORLDACCELERATOR_UPDATEALERT_EMAIL' => '',
				'EWORLDACCELERATOR_UPDATEALERT_DAYS' => 1,
			);
		}
	}

	/**
	 * Save form data.
	 */
	protected function postProcess() {
		$enabled = trim(Tools::getValue('EWORLDACCELERATOR_UPDATEALERT_ENABLED'));
		$emailList = trim(Tools::getValue('EWORLDACCELERATOR_UPDATEALERT_EMAIL'));
		$days = intval(trim(Tools::getValue('EWORLDACCELERATOR_UPDATEALERT_DAYS')));

		if ($enabled != 1 && $enabled != 2) {
			$this->errors[] = $this->displayError($this->l('Enabled value is not recognized'));
		}
		else {
			$oldValue = Configuration::get('EWORLDACCELERATOR_UPDATEALERT_ENABLED');
			Configuration::updateValue('EWORLDACCELERATOR_UPDATEALERT_ENABLED', $enabled);
			if ($enabled == 1 && $oldValue == 2) {
				$this->errors[] = $this->displayConfirmation($this->l('System enabled'));
			}
			else if ($enabled == 2 && $oldValue == 1) {
				$this->errors[] = $this->displayConfirmation($this->l('System disabled'));
			}
		}
		if ($emailList == '') {
			$this->errors[] = $this->displayError($this->l('Email list is empty. You must set at least 1 email'));
		}
		else {
			$listOk = true;
			$emailArray = explode(PHP_EOL, $emailList);
			if (sizeof($emailArray) > 0) {
				foreach ($emailArray as $currentEmail) {
					if (filter_var($currentEmail, FILTER_VALIDATE_EMAIL) === false) {
						$this->errors[] = $this->displayError($this->l($currentEmail.' is not a valid email address'));
						$listOk = false;
					}
				}
			}
			if ($listOk) {
				Configuration::updateValue('EWORLDACCELERATOR_UPDATEALERT_EMAIL', $emailList);
			}
		}
		if ($days < 1 || $days > 31) {
			$this->errors[] = $this->displayError($this->l('Days value is not correct'));
		}
		else {
			Configuration::updateValue('EWORLDACCELERATOR_UPDATEALERT_DAYS', $days);
		}
	}
}