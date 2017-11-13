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
 * @var \FostyFost\Drawwwr\Components\DrawwwrListComponent $component
 */

use \Bitrix\Main\Localization\Loc;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

Loc::loadMessages(__FILE__);

$this->addExternalCss("{$templateFolder}/css/vendor.css");

$this->addExternalCss("{$templateFolder}/css/style.css");

$this->addExternalJs("{$templateFolder}/js/vendor.js");

$this->addExternalJs("{$templateFolder}/js/script.js");
?>
    <div class="cont">
        <div class="drawwwr-gallery">
            <?php
            if (is_array($arResult["ITEMS"]) && !empty($arResult["ITEMS"])) {
                ?>
                <ul id="lightgallery">
                    <?php
                    foreach ($arResult["ITEMS"] as $arItem) {
                        if (!empty($arItem["IMAGES"])) {
                            ?>
                            <li
                                data-responsive="<?= $arItem["IMAGES"]["responsive"] ?>"
                                data-src="<?= $arItem["IMAGES"]["big"]["src"] ?>"
                                data-edit="<?= !empty($arItem["EDIT_PAGE_URL"])
                                    ? $arItem["EDIT_PAGE_URL"] : 'false' ?>"
                            >
                                <a href="#">
                                    <img
                                        class="img-responsive"
                                        src="<?= $arItem["IMAGES"]["thumb"]["src"] ?>"
                                    >
                                    <div class="drawwwr-gallery-poster">
                                        <?= $i ?>
                                        <img src="<?= "{$templateFolder}/img/zoom.png" ?>">
                                    </div>
                                </a>
                            </li>
                            <?php
                        }
                    }
                    ?>
                </ul>
                <?php
            }
            ?>
        </div>
    </div>
<?php
if (!empty($arResult["NAV_OBJECT"])) {
    ?>
    <div class="cont">
        <?php
        $APPLICATION->IncludeComponent(
            'fostyfost:drawwwr.pagenavigation',
            '.default',
            [
                "NAV_OBJECT" => $arResult["NAV_OBJECT"],
                "SEF_MODE"   => 'N'
            ],
            $component,
            ["HIDE_ICONS" => 'Y']
        );
        ?>
    </div>
    <?php
}
?>