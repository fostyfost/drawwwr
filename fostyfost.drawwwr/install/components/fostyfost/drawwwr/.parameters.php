<?php
/**
 * @var array $arCurrentValues
 */

use \Bitrix\Main\Localization\Loc;
use \FostyFost\Drawwwr\Helpers\ComponentParameters;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

if (!\Bitrix\Main\Loader::includeModule(DRAWWWR_MODULE_ID)) {
    return;
}

Loc::loadMessages(__FILE__);

$currentZone = basename(dirname(__DIR__));

try {
    $currentParameters = [
        "GROUPS"     => [
            "LIST"                 => [
                "NAME" => Loc::getMessage('FF_LIST'),
                "SORT" => 200
            ],
            "CREATION_AND_EDITING" => [
                "NAME" => Loc::getMessage('FF_CREATION_AND_EDITING'),
                "SORT" => 300
            ]
        ],
        "PARAMETERS" => [
            "SEF_MODE" => []
        ]
    ];

    $paramsDrawwwrList = ComponentParameters::getParameters(
        "{$currentZone}:drawwwr.list",
        [
            "COUNT"    => [
                "MOVE" => 'LIST'
            ],
            "EDIT_URL" => [
                "DELETE" => true
            ]
        ],
        $arCurrentValues
    );

    $paramsDrawwwrCreate = ComponentParameters::getParameters(
        "{$currentZone}:drawwwr.create",
        [
            "USE_COLOR_PICKER" => [
                "MOVE" => 'CREATION_AND_EDITING'
            ]
        ],
        $arCurrentValues
    );

    $paramsDrawwwrCreate = ComponentParameters::getParameters(
        "{$currentZone}:drawwwr.edit",
        [
            "USE_COLOR_PICKER" => [
                "MOVE" => 'CREATION_AND_EDITING'
            ],
            "ELEMENT_CODE"     => [
                "DELETE" => true
            ],
            "EDIT_URL"         => [
                "DELETE" => true
            ]
        ],
        $arCurrentValues
    );

    $arComponentParameters = array_replace_recursive(
        $currentParameters,
        $paramsDrawwwrList,
        $paramsDrawwwrCreate
    );
} catch (\Exception $e) {
    \ShowError($e->getMessage());
}
