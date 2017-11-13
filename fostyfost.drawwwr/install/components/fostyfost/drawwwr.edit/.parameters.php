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
        "GROUPS"     => [],
        "PARAMETERS" => [
            "ELEMENT_CODE"     => [
                "PARENT" => 'BASE',
                "NAME"   => Loc::getMessage('FF_ELEMENT_CODE'),
                "TYPE"   => 'string'
            ],
            "EDIT_URL"         => \CIBlockParameters::GetPathTemplateParam(
                "DETAIL",
                "EDIT_URL",
                Loc::getMessage('FF_EDIT_PAGE_URL'),
                "/edit/#ELEMENT_CODE#/",
                "URL_TEMPLATES"
            ),
            "USE_COLOR_PICKER" => [
                "PARENT"  => 'BASE',
                "NAME"    => Loc::getMessage('FF_USE_COLOR_PICKER'),
                "TYPE"    => 'CHECKBOX',
                "DEFAULT" => 'N'
            ]
        ]
    ];
} catch (\Exception $e) {
    \ShowError($e->getMessage());
}
