<?php
/**
 * @global \CMain $APPLICATION
 */

if (!defined('DRAWWWR_MODULE_ID')) {
    define('DRAWWWR_MODULE_ID', 'fostyfost.drawwwr');
}

require_once($_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/prolog_admin_before.php');

\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

$APPLICATION->SetTitle(\Bitrix\Main\Localization\Loc::getMessage('FF_DEBUG_SETTINGS'));

require_once($_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/prolog_admin_after.php');

$aTabs = [
    [
        "DIV"   => 'general',
        "TAB"   => \Bitrix\Main\Localization\Loc::getMessage('FF_DEBUG_GENERAL_SETTINGS'),
        "TITLE" => \Bitrix\Main\Localization\Loc::getMessage('FF_DEBUG_GENERAL_SETTINGS')
    ]
];

$tabControl = new \CAdminTabControl('tabControl', $aTabs);

$optionJquery = \Bitrix\Main\Config\Option::get(DRAWWWR_MODULE_ID, 'FF_DEBUG_JQUERY', 'N');

$optionGroups = explode(',', \Bitrix\Main\Config\Option::get(DRAWWWR_MODULE_ID, 'FF_DEBUG_GROUPS', ''));

$arGroups = \Bitrix\Main\GroupTable::getList([
    "select" => ["ID", "NAME"],
    "filter" => ["=ACTIVE" => 'Y'],
    "order"  => ["C_SORT" => 'asc']
])->fetchAll();
?>
    <form
        method="post"
        action="<?= $APPLICATION->GetCurPage() ?>"
        enctype="multipart/form-data"
        name="ff_debug_form"
        id="ff_debug_form"
    >
        <?php
        $tabControl->Begin();

        $tabControl->BeginNextTab();
        ?>
        <tr valign="top">
            <td
                width="40%"
                class="field-name"
            ><?= \Bitrix\Main\Localization\Loc::getMessage('FF_DEBUG_JQUERY') ?></td>
            <td valign="middle">
                <input type="hidden" name="FF_DEBUG_JQUERY" value="N">
                <input
                    title="<?= \Bitrix\Main\Localization\Loc::getMessage('FF_DEBUG_JQUERY') ?>"
                    type="checkbox" <?= $optionJquery === 'Y' ? 'checked="checked"' : '' ?>
                    name="FF_DEBUG_JQUERY"
                    value="Y"
                >
            </td>
        </tr>
        <tr valign="top">
            <td><?= \Bitrix\Main\Localization\Loc::getMessage('FF_DEBUG_GROUPS') ?></td>
            <td>
                <select
                    title="<?= \Bitrix\Main\Localization\Loc::getMessage('FF_DEBUG_GROUPS') ?>"
                    multiple="multiple"
                    name="FF_DEBUG_GROUPS[]"
                    size="8"
                >
                    <?php
                    foreach ($arGroups as $group) {
                        if (in_array($group["ID"], $optionGroups)) {
                            ?>
                            <option selected="selected" value="<?= $group["ID"] ?>"><?= $group["NAME"] ?></option>
                            <?php
                        } else {
                            ?>
                            <option value="<?= $group["ID"] ?>"><?= $group["NAME"] ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </td>
        </tr>
        <?php
        $tabControl->EndTab();

        $tabControl->Buttons();
        ?>
        <input type="submit" value="<?= \Bitrix\Main\Localization\Loc::getMessage('FF_DEBUG_SAVE') ?>">
        <?php
        $tabControl->End();
        ?>
    </form>
<?php
require($_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/epilog_admin.php');