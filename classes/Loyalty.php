<?php

class Loyalty extends ObjectModel
{
    public $id;

    /** @var string name */
    public $name;

    /** @var string description */
    public $description;

    /** @var string properties loyalty program */
    public $html_tags;

    /** @var bool */
    public $active = true;

    /** @Column(type="datetime") */
    public $date_end;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'fl_loyalty',
        'primary' => 'id_loyalty',
        'fields' => [
            'name' => ['type' => self::TYPE_STRING, 'required' => true],
            'description' => ['type' => self::TYPE_HTML, 'required' => true],
            'html_tags' => ['type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true],
            'date_end' => ['type' => self::TYPE_DATE],
        ],
    ];

    /**
     * Loyalty constructor.
     *
     * @param int|null $idLoyalty
     * @param int|null $idLang
     * @param int|null $idShop
     */
    public function __construct($idLoyalty = null, $idLang = null, $idShop = null)
    {
        parent::__construct($idLoyalty, $idLang, $idShop);
    }

    public static function getById($id)
    {
        return Db::getInstance()->ExecuteS("SELECT `id_loyalty` AS id, `name`, `description`, `html_tags`, `active`, `date_end` 
            FROM "._DB_PREFIX_."fl_loyalty 
            WHERE `id_loyalty` = $id");
    }

    public static function getPrograms()
	{
        $sql = 'SELECT id_loyalty, name, description, active, date_end
                FROM `'._DB_PREFIX_.'fl_loyalty`';

		$result = Db::getInstance()->ExecuteS($sql);

		if (!$result)
			return [];
		
		return $result;
	}

    public static function getProgramById($idLoyalty) {
        return DB::getInstance()->ExecuteS('SELECT `id_loyalty`, `name`, `description`, `active`, `date_end`
            FROM `'._DB_PREFIX_.'fl_loyalty` WHERE `id_loyalty` = '.$idLoyalty
        );
    }

    public function getHtmlTags() {
        if (empty($this->html_tags)) {
            return [];
        }

        return json_decode($this->html_tags);
    }

    public function setHtmlTags($data)
    {
        $this->html_tags = json_encode($data);
    }

}
