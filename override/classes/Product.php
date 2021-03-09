<?php

Class Product extends ProductCore {
    public static function getProductProperties($id_lang, $row, Context $context = null)
    {
        Hook::exec('actionGetProductPropertiesBefore', [
            'id_lang' => $id_lang,
            'product' => &$row,
            'context' => $context,
        ]);

        if (!$row['id_product']) {
            return false;
        }

        if ($context == null) {
            $context = Context::getContext();
        }

        $id_product_attribute = $row['id_product_attribute'] = (!empty($row['id_product_attribute']) ? (int) $row['id_product_attribute'] : null);

        // Product::getDefaultAttribute is only called if id_product_attribute is missing from the SQL query at the origin of it:
        // consider adding it in order to avoid unnecessary queries
        $row['allow_oosp'] = Product::isAvailableWhenOutOfStock($row['out_of_stock']);
        if (Combination::isFeatureActive() && $id_product_attribute === null
            && ((isset($row['cache_default_attribute']) && ($ipa_default = $row['cache_default_attribute']) !== null)
                || ($ipa_default = Product::getDefaultAttribute($row['id_product'], !$row['allow_oosp'])))) {
            $id_product_attribute = $row['id_product_attribute'] = $ipa_default;
        }
        if (!Combination::isFeatureActive() || !isset($row['id_product_attribute'])) {
            $id_product_attribute = $row['id_product_attribute'] = 0;
        }

        // Tax
        $usetax = !Tax::excludeTaxeOption();

        $cache_key = $row['id_product'] . '-' . $id_product_attribute . '-' . $id_lang . '-' . (int) $usetax;
        if (isset($row['id_product_pack'])) {
            $cache_key .= '-pack' . $row['id_product_pack'];
        }

        if (!isset($row['cover_image_id'])) {
            $cover = static::getCover($row['id_product']);
            if (isset($cover['id_image'])) {
                $row['cover_image_id'] = $cover['id_image'];
            }
        }

        if (isset($row['cover_image_id'])) {
            $cache_key .= '-cover' . (int) $row['cover_image_id'];
        }

        if (isset(self::$productPropertiesCache[$cache_key])) {
            return array_merge($row, self::$productPropertiesCache[$cache_key]);
        }

        // Datas
        $row['category'] = Category::getLinkRewrite((int) $row['id_category_default'], (int) $id_lang);
        $row['category_name'] = Db::getInstance()->getValue('SELECT name FROM ' . _DB_PREFIX_ . 'category_lang WHERE id_shop = ' . (int) $context->shop->id . ' AND id_lang = ' . (int) $id_lang . ' AND id_category = ' . (int) $row['id_category_default']);
        $row['link'] = $context->link->getProductLink((int) $row['id_product'], $row['link_rewrite'], $row['category'], $row['ean13']);

        $row['attribute_price'] = 0;
        if ($id_product_attribute) {
            $row['attribute_price'] = (float) Combination::getPrice($id_product_attribute);
        }

        if (isset($row['quantity_wanted'])) {
            // 'quantity_wanted' may very well be zero even if set
            $quantity = max((int) $row['minimal_quantity'], (int) $row['quantity_wanted']);
        } elseif (isset($row['cart_quantity'])) {
            $quantity = max((int) $row['minimal_quantity'], (int) $row['cart_quantity']);
        } else {
            $quantity = (int) $row['minimal_quantity'];
        }

        $row['price_tax_exc'] = Product::getPriceStatic(
            (int) $row['id_product'],
            false,
            $id_product_attribute,
            (self::$_taxCalculationMethod == PS_TAX_EXC ? 2 : 6),
            null,
            false,
            true,
            $quantity
        );

        if (self::$_taxCalculationMethod == PS_TAX_EXC) {
            $row['price_tax_exc'] = Tools::ps_round($row['price_tax_exc'], Context::getContext()->getComputingPrecision());
            $row['price'] = Product::getPriceStatic(
                (int) $row['id_product'],
                true,
                $id_product_attribute,
                6,
                null,
                false,
                true,
                $quantity
            );
            $row['price_without_reduction'] =
            $row['price_without_reduction_without_tax'] = Product::getPriceStatic(
                (int) $row['id_product'],
                false,
                $id_product_attribute,
                2,
                null,
                false,
                false,
                $quantity
            );
        } else {
            $row['price'] = Tools::ps_round(
                Product::getPriceStatic(
                    (int) $row['id_product'],
                    true,
                    $id_product_attribute,
                    6,
                    null,
                    false,
                    true,
                    $quantity
                ),
                Context::getContext()->getComputingPrecision()
            );
            $row['price_without_reduction'] = Product::getPriceStatic(
                (int) $row['id_product'],
                true,
                $id_product_attribute,
                6,
                null,
                false,
                false,
                $quantity
            );
            $row['price_without_reduction_without_tax'] = Product::getPriceStatic(
                (int) $row['id_product'],
                false,
                $id_product_attribute,
                6,
                null,
                false,
                false,
                $quantity
            );
        }

        $row['reduction'] = Product::getPriceStatic(
            (int) $row['id_product'],
            (bool) $usetax,
            $id_product_attribute,
            6,
            null,
            true,
            true,
            $quantity,
            true,
            null,
            null,
            null,
            $specific_prices
        );

        $row['reduction_without_tax'] = Product::getPriceStatic(
            (int) $row['id_product'],
            false,
            $id_product_attribute,
            6,
            null,
            true,
            true,
            $quantity,
            true,
            null,
            null,
            null,
            $specific_prices
        );

        $row['specific_prices'] = $specific_prices;

        $row['quantity'] = Product::getQuantity(
            (int) $row['id_product'],
            0,
            isset($row['cache_is_pack']) ? $row['cache_is_pack'] : null,
            $context->cart
        );

        $row['quantity_all_versions'] = $row['quantity'];

        if ($row['id_product_attribute']) {
            $row['quantity'] = Product::getQuantity(
                (int) $row['id_product'],
                $id_product_attribute,
                isset($row['cache_is_pack']) ? $row['cache_is_pack'] : null,
                $context->cart
            );

            $row['available_date'] = Product::getAvailableDate(
                (int) $row['id_product'],
                $id_product_attribute
            );
        }

        $row['id_image'] = Product::defineProductImage($row, $id_lang);
        $row['features'] = Product::getFrontFeaturesStatic((int) $id_lang, $row['id_product']);

        $row['attachments'] = [];
        if (!isset($row['cache_has_attachments']) || $row['cache_has_attachments']) {
            $row['attachments'] = Product::getAttachmentsStatic((int) $id_lang, $row['id_product']);
        }

        $row['virtual'] = ((!isset($row['is_virtual']) || $row['is_virtual']) ? 1 : 0);

        // Pack management
        $row['pack'] = (!isset($row['cache_is_pack']) ? Pack::isPack($row['id_product']) : (int) $row['cache_is_pack']);
        $row['packItems'] = $row['pack'] ? Pack::getItemTable($row['id_product'], $id_lang) : [];
        $row['nopackprice'] = $row['pack'] ? Pack::noPackPrice($row['id_product']) : 0;

        if ($row['pack'] && !Pack::isInStock($row['id_product'], $quantity, $context->cart)) {
            $row['quantity'] = 0;
        }

        $row['customization_required'] = false;
        if (isset($row['customizable']) && $row['customizable'] && Customization::isFeatureActive()) {
            if (count(Product::getRequiredCustomizableFieldsStatic((int) $row['id_product']))) {
                $row['customization_required'] = true;
            }
        }

        $attributes = Product::getAttributesParams($row['id_product'], $row['id_product_attribute']);

        foreach ($attributes as $attribute) {
            $row['attributes'][$attribute['id_attribute_group']] = $attribute;
        }

        $row = Product::getTaxesInformations($row, $context);

        $row['ecotax_rate'] = (float) Tax::getProductEcotaxRate($context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')});

        Hook::exec('actionGetProductPropertiesAfter', [
            'id_lang' => $id_lang,
            'product' => &$row,
            'context' => $context,
        ]);

        $combination = new Combination($id_product_attribute);

        if (0 != $combination->unit_price_impact && 0 != $row['unit_price_ratio']) {
            $unitPrice = ($row['price_tax_exc'] / $row['unit_price_ratio']) + $combination->unit_price_impact;
            $row['unit_price_ratio'] = $row['price_tax_exc'] / $unitPrice;
        }

        $row['unit_price'] = ($row['unit_price_ratio'] != 0 ? $row['price'] / $row['unit_price_ratio'] : 0);

        require_once(_PS_MODULE_DIR_.'/fl_loyalty/classes/Loyalty.php');

        $row['loyalty'] = [
            'flags' => Loyalty::getNamesPromotionsByProductId($row['id_product']),
            'promotions' => Loyalty::getDescriptionsPromotionsByProductId($row['id_product'])
        ];

        self::$productPropertiesCache[$cache_key] = $row;

        return self::$productPropertiesCache[$cache_key];
    }

}
