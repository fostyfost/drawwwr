<?php
/**
 * @global \CMain $APPLICATION
 */

use \Bitrix\Main\Localization\Loc;

if (!\check_bitrix_sessid()) {
    return false;
}

Loc::loadMessages(__FILE__);

echo (new \CAdminMessage([
    "TYPE"    => 'ERROR',
    "MESSAGE" => Loc::getMessage('FF_INSTALL_ERROR_BITRIX_VERSION_TITLE'),
    "DETAILS" => Loc::getMessage('FF_INSTALL_ERROR_BITRIX_VERSION_MESSAGE'),
    "HTML"    => true
]))->Show();

?>
<div style="margin-top: 20px;">
    <input
        onclick="location.href='update_system.php?lang=<?= LANGUAGE_ID ?>';"
        type="submit"
        class="adm-btn-save"
        value="<?= Loc::getMessage('FF_INSTALL_LINK_BITRIX_UPDATE') ?>"
    >
    <input
        onclick="location.href='<?= $APPLICATION->GetCurPage() ?>?lang=<?= LANGUAGE_ID ?>';"
        type="submit"
        value="<?= Loc::getMessage('FF_INSTALL_LINK_BACK_SOLUTIONS') ?>"
    >
</div>