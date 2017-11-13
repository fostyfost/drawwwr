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
