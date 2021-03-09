<?php

class Loyalty
{
    /**
     * Obtine el nombre de las promiciones asociadas al producto
     */
    public static function getNamesPromotionsByProductId($productId)
    {
        $today = date('Y-m-d H:i:s');

        $sql = "SELECT flp.promotion FROM "._DB_PREFIX_."fl_loyalty_promotions flp 
            JOIN "._DB_PREFIX_."fl_loyalty fl ON fl.id_loyalty = flp.id_loyalty
            WHERE flp.id_product = $productId AND fl.active = 1 AND fl.date_end > '".$today."'";

        
        return Db::getInstance()->ExecuteS($sql);
    }

    /**
     * Obtine la descripción de las promiciones asociadas al producto
     */
    public static function getDescriptionsPromotionsByProductId($productId)
    {
        $today = date('Y-m-d H:i:s');

        $sql = "SELECT flp.description FROM "._DB_PREFIX_."fl_loyalty_promotions flp 
            JOIN "._DB_PREFIX_."fl_loyalty fl ON fl.id_loyalty = flp.id_loyalty
            WHERE flp.id_product = $productId AND fl.active = 1 AND fl.date_end > '".$today."'";

        return Db::getInstance()->ExecuteS($sql);
    }
    
}
