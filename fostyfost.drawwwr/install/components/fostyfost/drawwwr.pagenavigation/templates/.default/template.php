<?php
/**
 * @var array $arParams
 * @var array $arResult
 * @var \CBitrixComponentTemplate $this
 * @var \FostyFost\Drawwwr\Components\DrawwwrPageNavigationComponent $component
 */

use \Bitrix\Main\Localization\Loc;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

Loc::loadMessages(__FILE__);

$component = $this->getComponent();

$this->setFrameMode(true);
?>
<div class="pagination">
    <div class="pagination-container row">
        <ul>
            <?php
            if ($arResult["REVERSED_PAGES"] === true) {
                if ($arResult["CURRENT_PAGE"] < $arResult["PAGE_COUNT"]) {
                    if (($arResult["CURRENT_PAGE"] + 1) == $arResult["PAGE_COUNT"]) {
                        ?>
                        <li class="page-prev"><a
                                href="<?= \htmlspecialcharsbx($arResult["URL"]) ?>"
                            ><span><?= Loc::getMessage('FF_NAV_BACK') ?></span></a></li>
                        <?php
                    } else {
                        ?>
                        <li class="page-prev"><a
                                href="<?= \htmlspecialcharsbx(
                                    $component->replaceUrlTemplate($arResult["CURRENT_PAGE"] + 1)
                                ) ?>"
                            ><span><?= Loc::getMessage('FF_NAV_BACK') ?></span></a></li>
                        <?php
                    }
                    ?>
                    <li><a href="<?= \htmlspecialcharsbx($arResult["URL"]) ?>"><span>1</span></a></li>
                    <?php
                } else {
                    ?>
                    <li class="page-prev"><span><?= Loc::getMessage('FF_NAV_BACK') ?></span></li>
                    <li class="active"><span>1</span></li>
                    <?php
                }

                $page = $arResult["START_PAGE"] - 1;

                while ($page >= $arResult["END_PAGE"] + 1) {
                    if ($page == $arResult["CURRENT_PAGE"]) {
                        ?>
                        <li class="active"><span><?= ($arResult["PAGE_COUNT"] - $page + 1) ?></span></li>
                        <?php
                    } else {
                        ?>
                        <li>
                            <a
                                href="<?= \htmlspecialcharsbx($component->replaceUrlTemplate($page)) ?>"
                            ><span><?= ($arResult["PAGE_COUNT"] - $page + 1) ?></span></a>
                        </li>
                        <?php
                    }

                    $page--;
                }

                if ($arResult["CURRENT_PAGE"] > 1) {
                    if ($arResult["PAGE_COUNT"] > 1) {
                        ?>
                        <li><a
                                href="<?= \htmlspecialcharsbx($component->replaceUrlTemplate(1)) ?>"
                            ><span><?= $arResult["PAGE_COUNT"] ?></span></a></li>
                        <?php
                    }
                    ?>
                    <li class="page-next"><a
                            href="<?= \htmlspecialcharsbx(
                                $component->replaceUrlTemplate($arResult["CURRENT_PAGE"] - 1)
                            ) ?>"
                        ><span><?= Loc::getMessage('FF_NAV_FORWARD') ?></span></a></li>
                    <?php
                } else {
                    if ($arResult["PAGE_COUNT"] > 1) {
                        ?>
                        <li class="active"><span><?= $arResult["PAGE_COUNT"] ?></span></li>
                        <?php
                    }
                    ?>
                    <li class="page-next"><span><?= Loc::getMessage('FF_NAV_FORWARD') ?></span></li>
                    <?php
                }
            } else {
                if ($arResult["CURRENT_PAGE"] > 1) {
                    if ($arResult["CURRENT_PAGE"] > 2) {
                        ?>
                        <li class="page-prev"><a
                                href="<?= \htmlspecialcharsbx(
                                    $component->replaceUrlTemplate($arResult["CURRENT_PAGE"] - 1)
                                ) ?>"
                            ><span><?= Loc::getMessage('FF_NAV_BACK') ?></span></a></li>
                        <?php
                    } else {
                        ?>
                        <li class="page-prev"><a
                                href="<?= \htmlspecialcharsbx($arResult["URL"]) ?>"
                            ><span><?= Loc::getMessage('FF_NAV_BACK') ?></span></a></li>
                        <?php
                    }
                    ?>
                    <li><a href="<?= \htmlspecialcharsbx($arResult["URL"]) ?>"><span>1</span></a></li>
                    <?php
                } else {
                    ?>
                    <li class="page-prev"><span><?= Loc::getMessage('FF_NAV_BACK') ?></span></li>
                    <li class="active"><span>1</span></li>
                    <?php
                }

                $page = $arResult["START_PAGE"] + 1;

                while ($page <= $arResult["END_PAGE"] - 1) {
                    if ($page == $arResult["CURRENT_PAGE"]) {
                        ?>
                        <li class="active"><span><?= $page ?></span></li>
                        <?php
                    } else {
                        ?>
                        <li>
                            <a
                                href="<?= \htmlspecialcharsbx($component->replaceUrlTemplate($page)) ?>"
                            ><span><?= $page ?></span></a>
                        </li>
                        <?php
                    }

                    $page++;
                }

                if ($arResult["CURRENT_PAGE"] < $arResult["PAGE_COUNT"]) {
                    if ($arResult["PAGE_COUNT"] > 1) {
                        ?>
                        <li>
                            <a
                                href="<?= \htmlspecialcharsbx(
                                    $component->replaceUrlTemplate($arResult["PAGE_COUNT"])
                                ) ?>"
                            ><span><?= $arResult["PAGE_COUNT"] ?></span></a>
                        </li>
                        <?php
                    }
                    ?>
                    <li class="page-next"><a
                            href="<?= \htmlspecialcharsbx(
                                $component->replaceUrlTemplate($arResult["CURRENT_PAGE"] + 1)
                            ) ?>"
                        ><span><?= Loc::getMessage('FF_NAV_FORWARD') ?></span></a></li>
                    <?php
                } else {
                    if ($arResult["PAGE_COUNT"] > 1) {
                        ?>
                        <li class="active"><span><?= $arResult["PAGE_COUNT"] ?></span></li>
                        <?php
                    }
                    ?>
                    <li class="page-next"><span><?= Loc::getMessage('FF_NAV_FORWARD') ?></span></li>
                    <?php
                }
            }

            if ($arResult["SHOW_ALL"]) {
                if ($arResult["ALL_RECORDS"]) {
                    ?>
                    <li class="page-all"><a
                            href="<?= \htmlspecialcharsbx($arResult["URL"]) ?>"
                            rel="nofollow"
                        ><span><?= Loc::getMessage('FF_NAV_PAGES') ?></span></a></li>
                    <?php
                } else {
                    ?>
                    <li class="page-all"><a
                            href="<?= \htmlspecialcharsbx($component->replaceUrlTemplate('all')) ?>"
                            rel="nofollow"
                        ><span><?= Loc::getMessage('FF_NAV_ALL') ?></span></a></li>
                    <?php
                }
            }
            ?>
        </ul>
        <div style="clear: both;"></div>
    </div>
</div>