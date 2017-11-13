<?php
/**
 * @var array $arCurrentValues
 */

use \Bitrix\Main\Localization\Loc;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

if (!\Bitrix\Main\Loader::includeModule(DRAWWWR_MODULE_ID)) {
    return;
}

Loc::loadMessages(__FILE__);

try {
    $arComponentParameters = [
        "GROUPS"     => [],
        "PARAMETERS" => [
            "FORM_TYPE"   => [
                "PARENT"   => 'BASE',
                "NAME"     => Loc::getMessage('FF_FORM_TYPE'),
                "TYPE"     => 'LIST',
                "VALUES"   => [
                    "PASSWORD" => Loc::getMessage('FF_PASSWORD'),
                    "ERROR"    => Loc::getMessage('FF_ERROR')
                ],
                "MULTIPLE" => 'N',
                "DEFAULT"  => [
                    "ERROR"
                ]
            ],
            "MESSAGE"     => [
                "PARENT"   => 'BASE',
                "NAME"     => Loc::getMessage('FF_MESSAGE'),
                "TYPE"     => 'STRING',
                "MULTIPLE" => 'N',
                "DEFAULT"  => Loc::getMessage('FF_ERROR_MESSAGE')
            ],
            "AJAX_PARAMS" => [
                "PARENT"   => 'BASE',
                "NAME"     => Loc::getMessage('FF_AJAX_PARAMS'),
                "TYPE"     => 'STRING',
                "MULTIPLE" => 'N',
                "DEFAULT"  => ''
            ]
        ]
    ];
} catch (\Exception $e) {
    \ShowError($e->getMessage());
}
