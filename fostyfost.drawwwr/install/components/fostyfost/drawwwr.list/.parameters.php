<?php
/**
 * @var array $arCurrentValues
 */

use \Bitrix\Main\Loader;
use \Bitrix\Main\Localization\Loc;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

if (!Loader::includeModule(DRAWWWR_MODULE_ID)) {
    return;
}

Loc::loadMessages(__FILE__);

try {
    Loader::includeModule('iblock');

    $arComponentParameters = [
        "GROUPS"     => [
            "OTHERS" => [
                "NAME" => Loc::getMessage('FF_LIST_GROUP_OTHERS'),
                "SORT" => 1100
            ]
        ],
        "PARAMETERS" => [
            "EDIT_URL" => \CIBlockParameters::GetPathTemplateParam(
                "DETAIL",
                "EDIT_URL",
                Loc::getMessage('FF_EDIT_PAGE_URL'),
                "/edit/#ELEMENT_CODE#/",
                "URL_TEMPLATES"
            ),
            "COUNT"           => [
                "PARENT"  => 'OTHERS',
                "NAME"    => Loc::getMessage('FF_COUNT'),
                "TYPE"    => 'STRING',
                "DEFAULT" => '10'
            ],
            "CACHE_TIME"      => [
                "DEFAULT" => 3600
            ]
        ]
    ];
} catch (\Exception $e) {
    \ShowError($e->getMessage());
}
