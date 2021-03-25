<?php
/**
* 2007-2021 PrestaShop
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2021 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once(_PS_MODULE_DIR_.'/fl_loyalty/classes/Loyalty.php');
require_once(_PS_MODULE_DIR_.'/fl_loyalty/classes/LoyaltyPromotion.php');

class Fl_loyalty extends Module
{
    protected $config_form = false;

    public $adminControllers = [
        'adminAjaxLoyalty' => 'AdminAjaxLoyalty',
        'adminAjaxLoyaltyPromotion' => 'AdminAjaxLoyaltyPromotion',
    ];

    public function __construct()
    {
        $this->name = 'fl_loyalty';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Farmalisto';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Programas de lealtad');
        $this->description = $this->l('Genera leyendas y flags promocionales de los programas de lealtad');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        require_once __DIR__ . '/sql/install.php';

        if (parent::install() &&
            $this->installTab() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('displayLoyaltyFlagPromotion') &&
            $this->registerHook('displayLoyaltyPromotion')) {
            return true;
        } else { // if something wrong return false
            $this->_errors[] = $this->l('There was an error during the uninstallation. Please contact us through Addons website.');

            return false;
        }
    }

    public function uninstall()
    {
        require_once __DIR__ . '/sql/uninstall.php';

        if (parent::uninstall() && $this->uninstallTab()) {
            return true;
        } else {
            $this->_errors[] = $this->l('There was an error on module uninstall. Please contact us through Addons website');

            return false;
        }
    }

    /**
     * This method is often use to create an ajax controller
     *
     * @return bool
     */
    public function installTab()
    {
        $result = true;

        foreach ($this->adminControllers as $controller_name) {
            $tab = new Tab();
            $tab->class_name = $controller_name;
            $tab->module = $this->name;
            $tab->active = true;
            $tab->id_parent = -1;
            $tab->name = array_fill_keys(
                Language::getIDs(false),
                $this->displayName
            );
            $result = $result && $tab->add();
        }

        return $result;
    }

    /**
     * uninstall tab
     *
     * @return bool
     */
    public function uninstallTab()
    {
        $result = true;

        foreach ($this->adminControllers as $controller_name) {
            $id_tab = (int) Tab::getIdFromClassName($controller_name);
            $tab = new Tab($id_tab);

            if (Validate::isLoadedObject($tab)) {
                $result = $result && $tab->delete();
            }
        }

        return $result;
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitFl_loyaltyModule')) == true) {
            $this->postProcess();
        }

        $sql = "SELECT fl.id_loyalty, fl.name, fl.description, fl.active FROM "._DB_PREFIX_."fl_loyalty fl";
        
       $programs = Db::getInstance()->ExecuteS($sql);

       $loyalty_ajax_link = $this->context->link->getAdminLink('AdminAjaxLoyalty');
       $loyalty_promotions_ajax_link = $this->context->link->getAdminLink('AdminAjaxLoyaltyPromotion');

       Media::addJsDef(array("loyalty_ajax_link" => $loyalty_ajax_link, "loyalty_promotions_ajax_link" => $loyalty_promotions_ajax_link));

        $this->context->smarty->assign([
            'programs' => Loyalty::getPrograms(),
            'module_dir' => $this->_path,
            ]);

        $this->context->controller->addJS('https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.15.3/xlsx.full.min.js');
        $this->context->controller->addJS($this->_path.'views/js/loyalty.js');
        $this->context->controller->addJS($this->_path.'views/js/loyalty-promotions.js');
        $this->context->controller->addJS($this->_path.'views/js/config.js');

        $this->context->controller->addCSS($this->_path.'views/css/config.css');

        return $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        die('Procesar datos TODO::333');
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookBackOfficeHeader()
    {
        $this->context->controller->addJS($this->_path.'views/js/back.js');
        $this->context->controller->addCSS($this->_path.'views/css/back.css');
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'views/js/front.js');
        $this->context->controller->addCSS($this->_path.'views/css/front.css');
    }

    /**
     * Muestra el nombre de la promoción
     */
    public function hookDisplayLoyaltyFlagPromotion(array $params)
    {
        $productId = Tools::getValue('id_product') ? Tools::getValue('id_product') : $params['product_id'];
        
        if ($productId) {
            $promotions = LoyaltyPromotion::getNamesPromotionsByProductId($productId);

            $this->context->smarty->assign('promotions', $promotions);

            return $this->context->smarty->fetch($this->local_path.'views/templates/front/flag-promotion.tpl');
        }
    }

    /**
     * Muestra la descripción de la promoción
     */
    public function hookDisplayLoyaltyPromotion(array $params)
    {
        $productId = Tools::getValue('id_product');
        
        if ($productId) {
            $promotions = LoyaltyPromotion::getDescriptionsPromotionsByProductId($productId);

            $this->context->smarty->assign('promotions', $promotions);

            return $this->context->smarty->fetch($this->local_path.'views/templates/front/promotion.tpl');
        }

    }
}
