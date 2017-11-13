<?php
/**
 * @global \CMain $APPLICATION
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$request = \Bitrix\Main\Context::getCurrent()->getRequest();

$showPanel = $request->get('SHOW_PANEL') === 'Y';

$zIndex = (int)$request->get('BX_PANEL_Z_INDEX');

unset($request);

?><!DOCTYPE html>
<html lang="<?= LANGUAGE_ID ?>">
<head>
    <meta charset="<?= LANG_CHARSET ?>">
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
    <?php
    $APPLICATION->ShowMeta('robots', false, false);

    $APPLICATION->ShowMeta('keywords', false, false);

    $APPLICATION->ShowMeta('description', false, false);

    $APPLICATION->ShowLink('canonical', null, false);

    $APPLICATION->ShowCSS(true, false);

    $APPLICATION->ShowHeadStrings();

    $APPLICATION->ShowHeadScripts();
    ?>
    <title><?php
        $APPLICATION->ShowTitle();
        ?></title>
    <link rel="shortcut icon" type="image/x-icon" href="<?= SITE_DIR ?>favicon.ico">
</head>
<body>
<?php
if ($showPanel) {
    if ($zIndex > 0) {
        echo "<div style=\"z-index: {$zIndex} !important; position: relative;\">";
        $APPLICATION->ShowPanel();
        echo '</div>';
    } else {
        $APPLICATION->ShowPanel();
    }
}
