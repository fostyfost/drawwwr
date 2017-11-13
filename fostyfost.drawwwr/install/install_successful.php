<?php
/**
 * @global \CMain $APPLICATION
 */

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$wizardInstallPath = '/bitrix/admin/wizard_install.php';

$session = explode('=', \bitrix_sessid_get());

echo (new \CAdminMessage([
    "TYPE"    => 'OK',
    "MESSAGE" => Loc::getMessage('FF_INSTALL_COMPLETE_TITLE'),
    "DETAILS" => Loc::getMessage('FF_INSTALL_COMPLETE_MESSAGE', ["#LOG#" => $GLOBALS["iblockCreationLog"]]),
    "HTML"    => true
]))->Show();
?>
<div style="margin-top: 20px;">
    <form action="<?= $wizardInstallPath ?>" method="get">
        <input
            type="hidden"
            name="lang"
            value="<?= LANG ?>"
        >
        <input
            type="hidden"
            name="<?= $session[0] ?>"
            value="<?= $session[1] ?>"
        >
        <input
            type="hidden"
            name="wizardName"
            value="fostyfost.drawwwr:fostyfost:drawwwr"
        >
        <input
            type="button"
            onclick="onBackClick();"
            value="<?= Loc::getMessage('FF_INSTALL_LINK_BACK_SOLUTIONS') ?>"
        >
        <input
            type="submit"
            value="<?= Loc::getMessage('FF_START_MASTER') ?>"
        >
    </form>
</div>
<script>
    function onBackClick () {
        window.location.reload();
    }
</script>
