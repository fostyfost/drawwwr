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
 * @var \FostyFost\Drawwwr\Components\DrawwwrAuthComponent $component
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
<?php
if ($arParams["FORM_TYPE"] === 'PASSWORD') {
    ?>
    <div id="ff-auth" class="ff-auth">
        <form
            id="ff_auth_form"
            class="ff-auth__form"
            name="AUTH_FORM"
            target="_top"
            autocomplete="off"
        >
            <?= bitrix_sessid_post() ?>
            <input type="hidden" name="AUTH" value="Y">
            <input type="hidden" name="AJAX_PARAMS" value="<?= $arParams["AJAX_PARAMS"] ?>">
            <label
                for="ff_auth_password"
                class="ff-auth__label"
            ><span
                    class="fa fa-unlock-alt"
                ></span> <?= Loc::getMessage('FF_PASSWORD_REQUIRED_MESSAGE') ?></label>
            <input
                id="ff_auth_password"
                class="ff-auth__input"
                name="DRAWWWR_PASSWORD"
                type="password"
                form="ff_auth_form"
                placeholder="********"
                autocomplete="off"
                maxlength="16"
            >
            <p
                id="ff_auth_error_message"
                class="ff-auth__error-message"
            >&nbsp;</p>
            <div class="ff-auth__buttons-container">
                <button
                    id="ff_auth_submit_button"
                    name="AUTH_SUBMIT"
                    class="ff-auth__button"
                ><span
                        class="fa fa-unlock"
                    ></span> <?= Loc::getMessage('FF_OK') ?></button>
                <button
                    id="ff_auth_back_button"
                    class="ff-auth__button"
                    type="button"
                    onclick="history.back();"
                ><span
                        class="fa fa-chevron-left"
                    ></span> <?= Loc::getMessage('FF_BACK') ?></button>
            </div>
        </form>
    </div>
    <script>
        window.drawwwrData = {
            wrongPassword: '<?= Loc::getMessage('FF_WRONG_PASSWORD') ?>',
            incorrectPasswordLength: '<?= Loc::getMessage('FF_INCORRECT_PASSWORD_LENGTH') ?>',
            unexpectedError: '<?= Loc::getMessage('FF_UNEXPECTED_ERROR') ?>',
            ajaxPath: '<?= "{$componentPath}/ajax.php" ?>'
        };

        localStorage.setItem('ENCRYPTED_H5I', '<?= $arResult["ENCRYPTED_H5I"] ?>');
    </script>
    <?php
} else {
    ?>
    <div id="ff-auth" class="ff-auth">
        <div class="ff-auth__message">
            <p
                class="ff-auth__error-message ff-auth__error-message--visible"
            ><?= $arParams["MESSAGE"] ?></p>
            <div class="ff-auth__buttons-container">
                <button
                    id="ff_auth_back_button"
                    class="ff-auth__button"
                    type="button"
                    onclick="goBack(); return !1;"
                ><span
                        class="fa fa-chevron-left"
                    ></span> <?= Loc::getMessage('FF_BACK') ?></button>
            </div>
        </div>
    </div>
    <?php
}