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
 * @var \FostyFost\Drawwwr\Components\DrawwwrCreateComponent $component
 */

use \Bitrix\Main\Localization\Loc;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

Loc::loadMessages(__FILE__);

$this->addExternalCss("{$templateFolder}/css/vendor.css");

$this->addExternalCss("{$templateFolder}/css/style.css");

if ($arParams["USE_COLOR_PICKER"] === 'Y') {
    $this->addExternalJs("{$templateFolder}/js/image.js");
}

$this->addExternalJs("{$templateFolder}/js/vendor.js");

$this->addExternalJs("{$templateFolder}/js/script.js");
?>
<div class="artboards"></div>
<div class="controls"><a
        href="#back"
        onClick="goBack(); return !1;"
        class="fa fa-chevron-left"
        title="<?= Loc::getMessage('FF_BACK') ?>"
    ></a><a
        href="#save"
        id="drawwwrSave"
        class="fa fa-save"
        title="<?= Loc::getMessage('FF_SAVE') ?>"
    ></a><a
        href="#import"
        class="fa fa-folder-open-o"
    ><input
            type="file"
            id="drawwwrImport"
            title="<?= Loc::getMessage('FF_IMPORT_IMAGE_FILE') ?>"
        ></a><a
        href="#export_png"
        id="drawwwrExportPng"
        title="<?= Loc::getMessage('FF_EXPORT_AS_PNG') ?>"
    ><span class="fa fa-mail-forward"></span> PNG</a><a
        href="#export_h5i"
        id="drawwwrExportH5i"
        title="<?= Loc::getMessage('FF_EXPORT_AS_H5I') ?>"
    ><span class="fa fa-mail-forward"></span> H5I</a><!--<a
        href="#remove"
        onclick="return !1;"
        title="<?/*= Loc::getMessage('FF_REMOVE') */?>"
    ><span class="fa fa-trash"></span></a>--></div>
<div class="brushes"></div>
<div class="sidebar">
    <div class="controls-section top">
        <div class="controls layers"><a
                href="#add"
                id="drawwwrAdd"
                class="fa fa-plus"
                title="<?= Loc::getMessage('FF_ADD_LAYER') ?>"
            ></a>
            <div class="h1"><?= Loc::getMessage('FF_LAYERS') ?></div>
            <ul></ul>
        </div>
    </div>
    <div class="controls-section bottom">
        <div class="controls thickness">
            <div class="h1"><span class="current-thickness">10</span> <?= Loc::getMessage('FF_THICKNESS') ?></div>
            <input
                type="range"
                min="1"
                max="100"
                value="10"
                name="thickness"
                title="<?= Loc::getMessage('FF_THICKNESS') ?>"
            >
        </div>
        <?php
        if ($arParams["USE_COLOR_PICKER"] === 'Y') {
            ?>
            <div class="controls color-picker"><span
                    class="current-color"
                    style="background-color: #000;"
                ></span>
                <div class="h1"><?= Loc::getMessage('FF_COLOR') ?></div>
            </div>
            <?php
        }
        ?>
    </div>
</div>
<div
    id="dialogForm"
    class="dialog-form"
    title="<?= Loc::getMessage('FF_DIALOG_TITLE') ?>"
>
    <p id="validateTips" class="message-p"><?= Loc::getMessage('FF_PASSWORD_REQUIRED') ?></p>
    <form id="creationForm" autocomplete="off">
        <label for="password"><?= Loc::getMessage('FF_PASSWORD') ?></label>
        <input
            autocomplete="off"
            type="password"
            name="DRAWWWR_PASSWORD"
            id="password"
            class="text ui-widget-content ui-corner-all"
            maxlength="16"
        >
        <?= \bitrix_sessid_post() ?>
        <input type="hidden" name="CREATE" value="Y">
        <input
            id="hiddenSubmit"
            class="hidden-submit"
            type="submit"
            tabindex="-1"
        >
    </form>
</div>
<div
    id="dialogError"
    class="dialog-form"
    title="<?= Loc::getMessage('FF_ERROR_TITLE') ?>"
>
    <p
        id="errorMessageText"
        class="message-p"
    ><?= Loc::getMessage('FF_UNEXPECTED_ERROR') ?></p>
</div>
<div
    id="dialogSuccess"
    class="dialog-form"
    title="<?= Loc::getMessage('FF_SUCCESS_TITLE') ?>"
><p
        id="successMessageText"
        class="message-p"
    ><?= Loc::getMessage('FF_SUCCESS') ?></p>
</div>
<script>
    window.drawwwrData = {
        layer: '<?= Loc::getMessage('FF_LAYER') ?>',
        def: '<?= Loc::getMessage('FF_DEF') ?>',
        pencil: '<?= Loc::getMessage('FF_PENCIL') ?>',
        eraser: '<?= Loc::getMessage('FF_ERASER') ?>',
        deleteConfirmation: '<?= Loc::getMessage('FF_DELETE_CONFIRMATION') ?>',
        newName: '<?= Loc::getMessage('FF_NEW_NAME') ?>',
        incorrectPasswordLength: '<?= Loc::getMessage('FF_INCORRECT_PASSWORD_LENGTH') ?>',
        saveButtonText: '<?= Loc::getMessage('FF_SAVE_BUTTON_TEXT') ?>',
        cancelButtonText: '<?= Loc::getMessage('FF_CANCEL_BUTTON_TEXT') ?>',
        successMessage: '<?= Loc::getMessage('FF_SUCCESS') ?>',
        unexpectedError: '<?= Loc::getMessage('FF_UNEXPECTED_ERROR') ?>',
        fileSizeError: '<?= Loc::getMessage('FF_FILE_SIZE_ERROR') ?>',
        ajaxPath: '<?= "{$componentPath}/ajax.php" ?>'
    };
</script>