<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class WtsResources extends Module
{
    public function __construct()
    {
        $this->name = 'wtsresources';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'genescu';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->l('WTS Resources');
        $this->description = $this->l('Allows adding external CSS and JS files.');
        $this->ps_versions_compliancy = array(
            'min' => '1.7.0.0',
            'max' => _PS_VERSION_
        );
    }

    public function install()
    {
        return parent::install() &&
            $this->registerHook('header') &&
            Configuration::updateValue('WTSRESOURCES_CSS_URLS', '') &&
            Configuration::updateValue('WTSRESOURCES_JS_URLS', '');
    }

    public function uninstall()
    {
        return parent::uninstall() &&
            Configuration::deleteByName('WTSRESOURCES_CSS_URLS') &&
            Configuration::deleteByName('WTSRESOURCES_JS_URLS');
    }

    public function getContent()
    {
        $output = '';
        if (Tools::isSubmit('submit' . $this->name)) {
            $css_urls = Tools::getValue('WTSRESOURCES_CSS_URLS');
            $js_urls = Tools::getValue('WTSRESOURCES_JS_URLS');
            Configuration::updateValue('WTSRESOURCES_CSS_URLS', $css_urls);
            Configuration::updateValue('WTSRESOURCES_JS_URLS', $js_urls);
            $output .= $this->displayConfirmation($this->l('Settings updated'));
        }

        return $output . $this->renderForm();
    }

    protected function renderForm()
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = (int)Configuration::get('PS_LANG_DEFAULT');
        $helper->allow_employee_back_office_view = true;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submit' . $this->name;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name
            . '&tab_module=' . $this->tab
            . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

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
                        'type' => 'textarea',
                        'label' => $this->l('External CSS URLs'),
                        'name' => 'WTSRESOURCES_CSS_URLS',
                        'desc' => $this->l('Enter one URL per line'),
                        'cols' => 60,
                        'rows' => 10,
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('External JS URLs'),
                        'name' => 'WTSRESOURCES_JS_URLS',
                        'desc' => $this->l('Enter one URL per line'),
                        'cols' => 60,
                        'rows' => 10,
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    protected function getConfigFormValues()
    {
        return array(
            'WTSRESOURCES_CSS_URLS' => Configuration::get('WTSRESOURCES_CSS_URLS', ''),
            'WTSRESOURCES_JS_URLS' => Configuration::get('WTSRESOURCES_JS_URLS', ''),
        );
    }

    public function hookHeader()
    {
        if (!$this->context->controller->controller_type == 'front') {
            return;
        }
        $this->addExternalResources();
    }

    protected function addExternalResources()
    {
        $css_urls = explode(PHP_EOL, Configuration::get('WTSRESOURCES_CSS_URLS', ''));
        foreach ($css_urls as $css_url) {
            $css_url = trim($css_url);
            if ($css_url) {
                $this->context->controller->registerStylesheet(sha1($css_url), $css_url, array('server' => 'remote'));
            }
        }

        $js_urls = explode(PHP_EOL, Configuration::get('WTSRESOURCES_JS_URLS', ''));
        foreach ($js_urls as $js_url) {
            $js_url = trim($js_url);
            if ($js_url) {
                $this->context->controller->registerJavascript(sha1($js_url), $js_url, array('server' => 'remote'));
            }
        }
    }
}
