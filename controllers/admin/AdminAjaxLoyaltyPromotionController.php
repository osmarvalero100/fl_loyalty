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
require_once(_PS_MODULE_DIR_.'/fl_loyalty/classes/LoyaltyPromotion.php');

class AdminAjaxLoyaltyPromotionController extends ModuleAdminController
{
    public function ajaxProcessSave()
    {
        $idLoyalty = Tools::getValue('id_loyalty');
        $loyalty = new Loyalty($idLoyalty);

        $promotionDescription = Tools::getValue('description');
        if (!empty($loyalty->getHtmlTags())) {
            foreach ($loyalty->getHtmlTags() as $key => $value) {
                $property = json_decode(json_encode($value), true);
               
                if ($property['element'] == 'strong') {
                    $strong = sprintf('<strong>%s</strong>', $property['text']);
                    $promotionDescription = str_replace($property['text'], $strong, $promotionDescription);
                }
                if ($property['element'] == 'a') {
                    $link = sprintf('<a href="%s" target="%s" title="%s">%s</a>', $property['url'], $property['target'], $property['text'], $property['text']);
                    $promotionDescription = str_replace($property['text'], $link, $promotionDescription);
                }
            }
        }

        $productId = Tools::getValue('id_product');
        if (empty($productId)) {
            $productId = LoyaltyPromotion::getProductIdByEan13(Tools::getValue('ean13'), $this->context->shop->id);
        }

        $loyaltyPromotion = new LoyaltyPromotion();
        $loyaltyPromotion->id_product = $productId;
        $loyaltyPromotion->id_loyalty = $idLoyalty;
        $loyaltyPromotion->id_shop = $this->context->shop->id;
        $loyaltyPromotion->promotion = Tools::getValue('promotion');
        $loyaltyPromotion->description = $promotionDescription;

        try {
            $loyaltyPromotion->save();
            $this->ajaxRender(json_encode($loyaltyPromotion));
        } catch (Exception $e) {
            $data = ['error' => $e->getMessage()];
            $this->ajaxRender(json_encode($data));
        }
        
    }

    public function ajaxProcessDeleteByLoyalty()
    {
        $idLoyalty = (int)Tools::getValue('id_loyalty');
        $idShop = (int)$this->context->shop->id;

        try {
            LoyaltyPromotion::deleteByLoyalty($idLoyalty, $idShop);
            $this->ajaxRender(json_encode(['success' => true]));
        } catch (Exception $e) {
            $this->ajaxRender(json_encode(['error' => $e->getMessage()]));
        }
    }
}
