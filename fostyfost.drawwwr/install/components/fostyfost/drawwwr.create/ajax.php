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

if (!$ajax->isCreate()) {
    die();
}

if (!$ajax->checkSession()) {
    die();
}

if (!$ajax->checkIblockModule()) {
    die();
}

if (!$ajax->checkDrawwwrModule()) {
    die();
}

$ajax->createIblockObject();

$ajax->createDrawwwrObject();

$ajax->setH5i();

if (!$ajax->checkH5iIsEmpty()) {
    die();
}

if (!$ajax->checkH5iLength()) {
    die();
}

$ajax->setImage();

if (!$ajax->checkImageIsEmpty()) {
    die();
}

if (!$ajax->checkImageLength()) {
    die();
}

if (!$ajax->checkDecryptionStatus(["DRAWWWR_PASSWORD"])) {
    die();
}

$ajax->setPassword(true);

if (!$ajax->checkPasswordIsEmpty()) {
    die();
}

if (!$ajax->checkPasswordMinLength()) {
    die();
}

if (!$ajax->checkPasswordMaxLength()) {
    die();
}

if (!$ajax->checkCreationImageFile()) {
    die();
}

if (!$ajax->checkCreationH5iFile()) {
    die();
}

if (!$ajax->checkCreationIblockElement()) {
    die();
}

if (!$ajax->checkRegistrationDrawwwrElement()) {
    die();
}

$ajax->sendSuccessResponse();

die();
