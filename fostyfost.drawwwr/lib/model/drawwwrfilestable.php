<?php

namespace FostyFost\Drawwwr\Model;

use \Bitrix\Main\Entity;
use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class DrawwwrFilesTable
 *
 * @package FostyFost\Drawwwr\Model
 */
class DrawwwrFilesTable extends Entity\DataManager
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
        return 'drawwwr_files';
    }

    /**
     * @return array
     */
    public static function getMap()
    {
        return [
            new Entity\IntegerField(
                'ID',
                [
                    "autocomplete" => true,
                    "primary"      => true,
                    "title"        => Loc::getMessage('FF_DRAWWWR_ID')
                ]
            ),
            new Entity\BooleanField(
                'CAN_EDIT',
                [
                    "values"        => ["N", "Y"],
                    "default_value" => 'N',
                    "title"         => Loc::getMessage('FF_DRAWWWR_CAN_EDIT')
                ]
            ),
            new Entity\IntegerField(
                'IMAGE_FILE_ID',
                [
                    "required"   => true,
                    "title"      => Loc::getMessage('FF_DRAWWWR_H5I_FILE_ID'),
                    "validation" => function () {
                        return [
                            new Entity\Validator\Unique()
                        ];
                    },
                ]
            ),
            new Entity\IntegerField(
                'H5I_FILE_ID',
                [
                    "required"   => true,
                    "title"      => Loc::getMessage('FF_DRAWWWR_H5I_FILE_ID'),
                    "validation" => function () {
                        return [
                            new Entity\Validator\Unique()
                        ];
                    },
                ]
            ),
            new Entity\IntegerField(
                'IBLOCK_ELEMENT_ID',
                [
                    "required"   => 'true',
                    "title"      => Loc::getMessage('FF_DRAWWWR_IBLOCK_ELEMENT_ID'),
                    "validation" => function () {
                        return [
                            new Entity\Validator\Unique()
                        ];
                    }
                ]
            ),
            new Entity\StringField(
                'PASSWORD',
                [
                    "required"   => true,
                    "title"      => Loc::getMessage('FF_DRAWWWR_PASSWORD'),
                    "validation" => function () {
                        return [
                            new Entity\Validator\Length(null, 255),
                        ];
                    },
                ]
            ),
            new Entity\ReferenceField(
                'FILE',
                '\\Bitrix\\Main\\File',
                ["=this.H5I_FILE_ID" => 'ref.ID']
            ),
            new Entity\ReferenceField(
                'ELEMENT',
                '\\Bitrix\\Iblock\\Element',
                ["=this.IBLOCK_ELEMENT_ID" => 'ref.ID']
            )
        ];
    }
}
