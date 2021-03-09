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

class Fl_loyalty extends Module
{
    protected $config_form = false;

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

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('displayLoyaltyFlagPromotion') &&
            $this->registerHook('displayLoyaltyPromotion');
    }

    public function uninstall()
    {
        require_once __DIR__ . '/sql/uninstall.php';

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
        if (((bool)Tools::isSubmit('submitFl_loyaltyModule')) == true) {
            $this->postProcess();
        }

        $sql = "SELECT fl.id_loyalty, fl.name, fl.description, fl.active FROM "._DB_PREFIX_."fl_loyalty fl";
        
       $programs = Db::getInstance()->ExecuteS($sql);

        $this->context->smarty->assign([
            'programs' => $programs,
            'module_dir' => $this->_path
            ]);

        $this->context->controller->addJS('https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.15.3/xlsx.full.min.js');

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
        require_once(_PS_MODULE_DIR_.'/fl_loyalty/classes/Loyalty.php');

        $productId = Tools::getValue('id_product') ? Tools::getValue('id_product') : $params['product_id'];
        
        if ($productId) {
            $promotions = Loyalty::getNamesPromotionsByProductId($productId);

            $this->context->smarty->assign('promotions', $promotions);

            return $this->context->smarty->fetch($this->local_path.'views/templates/front/flag-promotion.tpl');
        }
    }

    /**
     * Muestra la descripción de la promoción
     */
    public function hookDisplayLoyaltyPromotion(array $params)
    {
        require_once(_PS_MODULE_DIR_.'/fl_loyalty/classes/Loyalty.php');

        $productId = Tools::getValue('id_product');
        
        if ($productId) {
            $promotions = Loyalty::getDescriptionsPromotionsByProductId($productId);

            $this->context->smarty->assign('promotions', $promotions);

            return $this->context->smarty->fetch($this->local_path.'views/templates/front/promotion.tpl');
        }

    }
}
