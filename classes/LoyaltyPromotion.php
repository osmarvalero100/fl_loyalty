<?php

class LoyaltyPromotion extends ObjectModel
{
    public $id;

    /** @var int Product id */
    public $id_product;

    /** @var int Loyalty id */
    public $id_Loyalty;

    /** @var int */
    public $id_shop;

    /** @var string promotion */
    public $promotion;

    /** @var string description */
    public $description;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'fl_loyalty_promotions',
        'primary' => 'id_loyalty_promotion',
        'fields' => [
            'id_product' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'id_loyalty' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'promotion' => ['type' => self::TYPE_STRING, 'required' => true, 'size' => 400],
            'description' => ['type' => self::TYPE_HTML, 'validate' => 'isCleanHtml'],
        ],
    ];

    /**
     * LoyaltyPromotion constructor.
     *
     * @param int|null $idLoyaltyPromotion
     * @param int|null $idLang
     * @param int|null $idShop
     */
    public function __construct($idLoyaltyPromotion = null, $idLang = null, $idShop = null)
    {
        parent::__construct($idLoyaltyPromotion, $idLang, $idShop);
    }

    public static function getProductIdByEan13($ean13, $idShop) 
    {
        $sql = "SELECT p.id_product FROM "._DB_PREFIX_."product p 
            JOIN "._DB_PREFIX_."product_shop ps ON ps.id_product = p.id_product
            WHERE ps.id_shop = ".$idShop." AND p.ean13 = '".$ean13."'";

        return Db::getInstance()->getValue($sql);
    }

    public static function deleteByLoyalty($idLoyalty, $idShop)
    {
        $sql = "DELETE FROM "._DB_PREFIX_."fl_loyalty_promotions 
        WHERE id_loyalty = $idLoyalty AND id_shop = $idShop";

        return Db::getInstance()->Execute($sql);
    }

    public static function getAllByIdLoyalty($idLoyalty, $idShop)
    {
        $sql = "SELECT id_loyalty_promotion, id_product, id_loyalty, promotion, description 
            FROM "._DB_PREFIX_."fl_loyalty_promotions 
            WHERE id_loyalty = $idLoyalty AND id_shop = $idShop";
        
        return Db::getInstance()->ExecuteS($sql);
    }

    /**
     * Obtine el nombre de las promiciones asociadas al producto
     */
    public static function getNamesPromotionsByProductId($productId)
    {
        $today = date('Y-m-d H:i:s');

        $sql = "SELECT flp.promotion FROM "._DB_PREFIX_."fl_loyalty_promotions flp 
            JOIN "._DB_PREFIX_."fl_loyalty fl ON fl.id_loyalty = flp.id_loyalty
            WHERE flp.id_product = $productId AND fl.active = 1 AND (fl.date_end > '".$today."' OR fl.date_end = '')";

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
            WHERE flp.id_product = $productId AND fl.active = 1 AND (fl.date_end > '".$today."' OR fl.date_end = '')";

        return Db::getInstance()->ExecuteS($sql);
    }
    
}
