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

use \Bitrix\Main\Localization\Loc;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$this->addExternalCss("{$templateFolder}/css/list.css");

Loc::loadMessages(__FILE__);
?>
    <div class="cont">
        <div class="page-head">
            <h1><?= Loc::getMessage('FF_DRAWWWR_TITLE') ?></h1>
            <p class="lead"><?= Loc::getMessage('FF_CTA_TEXT') ?></p>
            <?php
            if (!empty($arResult["URL_TEMPLATES"]["create"])) {
                ?>
                <a
                    href="<?= "{$arResult["FOLDER"]}{$arResult["URL_TEMPLATES"]["create"]}" ?>"
                    class="btn btn-primary btn-lg"
                ><?= Loc::getMessage('FF_CTA_BUTTON') ?></a>
                <?php
            }
            ?>
        </div>
    </div>
<?php
$APPLICATION->IncludeComponent(
    'fostyfost:drawwwr.list',
    $arParams["COMPONENT_TEMPLATE"],
    [
        "COUNT"      => $arParams["COUNT"],
        "CACHE_TIME" => $arParams["CACHE_TIME"],
        "CACHE_TYPE" => $arParams["CACHE_TYPE"],
        "EDIT_URL"   => "{$arResult["FOLDER"]}{$arResult["URL_TEMPLATES"]["edit"]}"
    ],
    $component,
    ["HIDE_ICONS" => 'Y']
);
