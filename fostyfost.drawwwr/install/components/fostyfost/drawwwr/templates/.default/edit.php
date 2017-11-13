<?php
/**
 * @var array $arParams
 * @var array $arResult
 * @global \CMain $APPLICATION
 * @global \CUser $USER
 * @global $DB
 * @var \CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $templateFile
 * @var string $templateFolder
 * @var string $componentPath
 * @var \FostyFost\Drawwwr\Components\DrawwwrComponent $component
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$APPLICATION->IncludeComponent(
    'fostyfost:drawwwr.edit',
    $arParams["COMPONENT_TEMPLATE"],
    [
        "ELEMENT_CODE"     => $arResult["VARIABLES"]["ELEMENT_CODE"],
        "EDIT_URL"         => "{$arResult["FOLDER"]}{$arResult["URL_TEMPLATES"]["edit"]}",
        "USE_COLOR_PICKER" => $arParams["USE_COLOR_PICKER"]
    ],
    $component
);
