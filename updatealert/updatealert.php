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

if (!defined('_PS_VERSION_')) {
    exit;
}

class UpdateAlert extends Module
{

    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'updatealert';
        $this->tab = 'administration';
        $this->version = '0.1.0';
        $this->author = 'eworld Accelerator';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        if (version_compare(_PS_VERSION_, '1.6', '>=')) {
            $this->bootstrap = true;
        }

        parent::__construct();

        $this->displayName = $this->l('Update Alert');
        $this->description = $this->l('Send you emails for each update (Prestashop or modules)');

        /* Have to force max to 1.7 cause compliancy is wrong in Prestashop module class */
        $this->ps_versions_compliancy = array('min' => '1.5', 'max' => '1.7');

        /* Backward compatibility */
        if (version_compare(_PS_VERSION_, '1.5', '<')) {
            require(_PS_MODULE_DIR_ . $this->name . '/backward_compatibility/backward.php');
        }
    }

    public function uninstall()
    {
        Configuration::deleteByName('EACC_UPDATEALERT_ENABLED');
        Configuration::deleteByName('EACC_UPDATEALERT_EMAIL');
        Configuration::deleteByName('EACC_UPDATEALERT_DAYS');
        Configuration::deleteByName('EACC_UPDATEALERT_CONTENT');
        Configuration::deleteByName('EACC_UPDATEALERT_LAST');

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
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitUpdateAlertModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');

        if (isset($this->errors) && is_array($this->errors) && count($this->errors) > 0) {
            $output .= join("\r\n", $this->errors);
        }

        return $output . $this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
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
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'radio',
                        'label' => $this->l('Enable system'),
                        'name' => 'EACC_UPDATEALERT_ENABLED',
                        'class' => 't',
                        'required' => true,
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',                           // The content of the 'id' attribute of the <input> tag, and of the 'for' attribute for the <label> tag.
                                'value' => 1,                                     // The content of the 'value' attribute of the <input> tag.
                                'label' => $this->l('Enabled')                    // The <label> for this radio button.
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 2,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Email list'),
                        'name' => 'EACC_UPDATEALERT_EMAIL',
                        'desc' => '1 email per line',
                        'required' => true
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Day(s) between alerts'),
                        'name' => 'EACC_UPDATEALERT_DAYS',
                        'class' => 'lg',
                        'required' => true,
                        'desc' => $this->l('Minimum: 1, Maximum: 31')
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
    protected function getConfigFormValues()
    {
        // Definied values
        $enabled = (int) trim(Configuration::get('EACC_UPDATEALERT_ENABLED'));
        /*var_dump(trim(Configuration::get('EACC_UPDATEALERT_ENABLED')));
        var_dump($enabled);exit;*/
        if ($enabled == 1 || $enabled == 2) {
            return array(
                'EACC_UPDATEALERT_ENABLED' => $enabled,
                'EACC_UPDATEALERT_EMAIL' => Configuration::get('EACC_UPDATEALERT_EMAIL'),
                'EACC_UPDATEALERT_DAYS' => Configuration::get('EACC_UPDATEALERT_DAYS'),
            );
        } // Default values
        else {
            return array(
                'EACC_UPDATEALERT_ENABLED' => 1,
                'EACC_UPDATEALERT_EMAIL' => '',
                'EACC_UPDATEALERT_DAYS' => 1,
            );
        }
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $enabled = trim(Tools::getValue('EACC_UPDATEALERT_ENABLED'));
        $emailList = trim(Tools::getValue('EACC_UPDATEALERT_EMAIL'));
        $days = (int)trim(Tools::getValue('EACC_UPDATEALERT_DAYS'));

        if ($enabled != 1 && $enabled != 2) {
            $this->errors[] = $this->displayError($this->l('Enabled value is not recognized'));
        } else {
            $oldValue = Configuration::get('EACC_UPDATEALERT_ENABLED');
            Configuration::updateValue('EACC_UPDATEALERT_ENABLED', $enabled);
            if ($enabled == 1 && $oldValue == 2) {
                $this->errors[] = $this->displayConfirmation($this->l('System enabled'));
            } else if ($enabled == 2 && $oldValue == 1) {
                $this->errors[] = $this->displayConfirmation($this->l('System disabled'));
            }
        }
        if ($emailList == '') {
            $this->errors[] = $this->displayError($this->l('Email list is empty. You must set at least 1 email'));
        } else {
            $listOk = true;
            $emailArray = explode(PHP_EOL, $emailList);
            if (count($emailArray) > 0) {
                foreach ($emailArray as $currentEmail) {
                    $currentEmail = trim($currentEmail);
                    if (filter_var($currentEmail, FILTER_VALIDATE_EMAIL) === false) {
                        $this->errors[] = $this->displayError($this->l($currentEmail . ' is not a valid email address'));
                        $listOk = false;
                    }
                }
            }
            if ($listOk) {
                Configuration::updateValue('EACC_UPDATEALERT_EMAIL', $emailList);
            }
        }
        if ($days < 1 || $days > 31) {
            $this->errors[] = $this->displayError($this->l('Days value is not correct'));
        } else {
            Configuration::updateValue('EACC_UPDATEALERT_DAYS', $days);
        }
    }
}

require_once 'UpdateAlertModule.php';
require_once 'UpdateAlertCron.php';
require_once 'UpdateAlertAlert.php';