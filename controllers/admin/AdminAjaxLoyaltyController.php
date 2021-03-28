<?php

/*
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
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @version  Release: $Revision: 13573 $
 *  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
require_once(_PS_MODULE_DIR_.'/fl_loyalty/classes/Loyalty.php');

/**
 * @property Loyalty $object
 */
class AdminAjaxLoyaltyController extends ModuleAdminController
{
    public function ajaxProcessGetPrograms()
    {
        if (Tools::getvalue('id_loyalty')) {
            $loyalty = new Loyalty((int)Tools::getvalue('id_loyalty'));
        } else {
            $loyalty = new Loyalty();
        }
        
        $loyalty->name = 'Hola Loyalty';
        
        $response = Tools::jsonEncode(Loyalty::getPrograms());

        $this->ajaxDie($response);
    }

    public function ajaxProcessGetById()
    {
        $this->ajaxRender(json_encode(Loyalty::getById((int)Tools::getvalue('id_loyalty'))[0]));
    }

    public function ajaxProcessSave()
    {
        if (!empty(Tools::getvalue('id_loyalty'))) {
            $loyalty = new Loyalty((int)Tools::getvalue('id_loyalty'));
        } else {
            $loyalty = new Loyalty();
        }

        if (!empty(Tools::getvalue('properties'))) {
            $loyalty = new Loyalty((int)Tools::getvalue('id_loyalty'));
            // Create property
            if (Tools::getvalue('properties') == 'save') {
                $property = [
                    "id" => uniqid(),
                    "element" => Tools::getvalue('element'),
                    "text" => Tools::getvalue('propText'),
                ];

                if (Tools::getvalue('element') == 'a') {
                    $property["url"] = Tools::getvalue('propUrl');
                    $property["target"] = Tools::getvalue('propUrlTarget');
                }

                $properties = (array)$loyalty->getHtmlTags();
                $properties[] = $property;
                
                $loyalty->setHtmlTags($properties);
            }
            // Delete property
            if (Tools::getvalue('properties') == 'delete') {
                $properties = [];

                foreach ($loyalty->getHtmlTags() as $key => $value) {
                    $property = json_decode(json_encode($value), true);
                   
                    if ($property['id'] != (string)Tools::getvalue('id_property')) {
                        $properties[] = (array)$value;
                    }
                }
                $loyalty->setHtmlTags($properties);
            }
        } else {
            $loyalty->name = Tools::getvalue('name');
            $loyalty->description = Tools::getvalue('description');
            $loyalty->html_tags = '';
            $loyalty->date_end = Tools::getvalue('date_end');

            if (!empty(Tools::getvalue('active'))) {
                $loyalty->active = Tools::getvalue('active');
            }
        }

        try {
            $loyalty->save();
            $this->ajaxRender(json_encode($loyalty));
        } catch (Exception $e) {
            $data = ['error' => $e->getMessage()];
            $this->ajaxRender(json_encode($data));
        }        
    }

    public function ajaxProcessChangeStatus()
    {
        $loyalty = new Loyalty(Tools::getvalue('id_loyalty'));
        $loyalty->active = !$loyalty->active;

        try {
            $loyalty->save();
            $this->ajaxRender(json_encode($loyalty));
        } catch (Exception $e) {
            $this->ajaxRender(json_encode(['error' => $e->getMessage()]));
        }
    }

    public function ajaxProcessRemove()
    {
        try {
            $loyalty = new Loyalty((int)Tools::getvalue('id_loyalty'));
            $loyalty->delete();

            $this->ajaxRender(json_encode(['success' => true]));
        } catch (Exception $e) {
            $this->ajaxRender(json_encode(['success' => false, 'message' => $e->getMessage()]));
        }
    }
}
