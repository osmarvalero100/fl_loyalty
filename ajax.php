<?php

include_once(dirname(__FILE__)."/../../config/config.inc.php");

$fieldsLoyalty = ['id_loyalty', 'name', 'description', 'html_tags', 'active'];

if (!empty($_GET['submit'])) {
    switch ($_GET['submit']) {
        case 'getLoyaltyProgramById':
            getLoyaltyProgramById($fieldsLoyalty);
            break;
        default:
            break;
    }
}

if (!empty($_POST['submit'])) {
    switch ($_POST['submit']) {
        case 'addLoyaltyProgram':
            saveLoyaltyProgram($fieldsLoyalty);
            break;
        case 'removeLoyaltyProgram':
            removeLoyaltyProgram();
            break;
        case 'savePromotions':
            savePromotions();
            break;
        default:
            break;
    }
}

exit(json_encode(["404" => 'Not found']));


function saveLoyaltyProgram($fieldsLoyalty)
{
    if (!empty($_POST[$fieldsLoyalty[0]])) {
        $idProgram = $_POST[$fieldsLoyalty[0]];
        $fieldsSets = '';
        $conditionSql = '';

        foreach ($fieldsLoyalty as $key=>$value) {
            if (!empty($_POST[$value])){
                if ($fieldsLoyalty[$key] == 'id_loyalty') {
                    $conditionSql = " WHERE id_loyalty = ".$_POST[$value];
                } else {
                    $fieldsSets .= " $value = '".$_POST[$value]."',";
                }
            }
        }

        $fieldsSets = rtrim($fieldsSets, ",");

        $sql = "UPDATE "._DB_PREFIX_."fl_loyalty SET $fieldsSets $conditionSql";

        //die($sql);

        if (!Db::getInstance()->Execute($sql)) {
            exit(json_encode(['success' => false]));
        }

        $whereCurrentProgram = "WHERE id_loyalty =  $idProgram";
    } else {
        $name = $_POST[$fieldsLoyalty[1]];
        $description = $_POST[$fieldsLoyalty[2]];
        
        $sql = "INSERT INTO "._DB_PREFIX_."fl_loyalty (name, description) VALUES('$name', '$description')";

        if (!Db::getInstance()->Execute($sql)) {
            exit(json_encode(['success' => false]));
        }

        $whereCurrentProgram = "ORDER BY fl.id_loyalty DESC LIMIT 1";

    }
    
    $sql = "SELECT fl.id_loyalty, fl.name, fl.description, fl.active FROM "._DB_PREFIX_."fl_loyalty fl $whereCurrentProgram";
    
    $lastProgram = Db::getInstance()->ExecuteS($sql);
        
    exit(json_encode(['success' => true, 'data' => $lastProgram]));
}

function getLoyaltyProgramById($fieldsLoyalty)
{
    if (!empty($_GET[$fieldsLoyalty[0]])) {
        $sql = "SELECT id_loyalty, name, description, active FROM "._DB_PREFIX_."fl_loyalty WHERE id_loyalty = ".$_GET[$fieldsLoyalty[0]];
    
        $program = Db::getInstance()->ExecuteS($sql);
            
        exit(json_encode($program));
    }
}

function savePromotions()
{
    //$filePromotios = file_get_contents($_FILES['promotions']['tmp_name']);

       //$objPHPExcel = PHPExcel_IOFactory::load($filePromotios);

    //$excelReader = IOFactory::createReaderForFile($filePromotios);
    return 0;
}

function removeLoyaltyProgram()
{
    $idLoyalty = $_POST['id_loyalty'];

    $sql = "DELETE FROM "._DB_PREFIX_."fl_loyalty WHERE id_loyalty = $idLoyalty";

    if (!Db::getInstance()->Execute($sql)) {
        exit(json_encode(['success' => false]));
    } 

    exit(json_encode(['success' => true]));
}
