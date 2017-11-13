<?php

namespace FostyFost\Drawwwr\Model;

use \Bitrix\Main\Entity\DataManager;

/**
 * Class IBlockElementPropertyTable
 *
 * @package FostyFost\Drawwwr\Model
 * В классе описывается ORM сущность со связями к сущностям
 * PROPERTY (для получения параметров),
 * ELEMENT (для получения данных элемента) и
 * ENUM (для получения значений списков)
 */
class IBlockElementPropertyTable extends DataManager
{
    /**
     * @return string
     */
    public static function getFilePath()
    {
        return __FILE__;
    }

    /**
     * @return string
     */
    public static function getTableName()
    {
        return 'b_iblock_element_property';
    }

    /**
     * @return array
     */
    public static function getMap()
    {
        return [
            "ID"                 => [
                "data_type"    => 'integer',
                "primary"      => true,
                "autocomplete" => true,
                "title"        => 'ID',
            ],
            "IBLOCK_PROPERTY_ID" => [
                "data_type" => 'integer',
                "primary"   => true,
            ],
            "IBLOCK_ELEMENT_ID"  => [
                "data_type" => 'integer',
                "primary"   => true,
            ],
            "VALUE"              => [
                "data_type" => 'string',
                "required"  => true,
            ],
            "VALUE_TYPE"         => [
                "data_type" => 'string',
                "required"  => true,
            ],
            "VALUE_ENUM"         => [
                "data_type" => 'integer',
            ],
            "VALUE_NUM"          => [
                "data_type" => 'float',
            ],
            "DESCRIPTION"        => [
                "data_type" => 'string',
            ],
            "PROPERTY"           => [
                "data_type" => '\\Bitrix\\Iblock\\Property',
                "reference" => [
                    "=this.IBLOCK_PROPERTY_ID" => 'ref.ID'
                ],
            ],
            "ELEMENT"            => [
                "data_type" => '\\Bitrix\\Iblock\\Element',
                "reference" => [
                    "=this.IBLOCK_ELEMENT_ID" => 'ref.ID'
                ],
            ],
            "ENUM"               => [
                "data_type" => '\\Bitrix\\Iblock\\PropertyEnumeration',
                "reference" => [
                    "=this.VALUE_ENUM" => 'ref.ID'
                ],
            ]
        ];
    }
}
