<?php

define('STOP_STATISTICS', true);
define('NO_AGENT_STATISTIC', true);

require($_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/prolog_before.php');

$ajax = new \FostyFost\Drawwwr\Helpers\AjaxHelper();

if (!$ajax->checkAjax()) {
    die();
}

if (!$ajax->checkPost()) {
    die();
}

if (!$ajax->isAuth()) {
    die();
}

if (!$ajax->checkSession()) {
    die();
}

if (!$ajax->checkDecryptionStatus(["DRAWWWR_PASSWORD"])) {
    die();
}

if (!$ajax->checkIblockModule()) {
    die();
}

if (!$ajax->checkDrawwwrModule()) {
    die();
}

$ajax->createDrawwwrObject();

if (!$ajax->checkAjaxParams()) {
    die();
}

if (!$ajax->checkDrawwwrElementId()) {
    die();
}

if (!$ajax->authorize()) {
    die();
}

$ajax->sendSuccessResponse();

die();
