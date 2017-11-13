<?php

define('STOP_STATISTICS', true);
define('NO_AGENT_STATISTIC', true);

require($_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/prolog_before.php');

$ajax = new \FostyFost\Drawwwr\Helpers\AjaxHelper();

if ($ajax->isLogOut()) {
    \BXClearCache(true, '/drawwwr/');

    \FostyFost\Drawwwr\AccessController::logOut();

    die();
}

if (!$ajax->checkAjax()) {
    die();
}

if (!$ajax->checkPost()) {
    die();
}

if (!$ajax->checkIblockModule()) {
    die();
}

if (!$ajax->checkDrawwwrModule()) {
    die();
}

if ($ajax->isUpdate()) {
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

    try {
        $ajax->getRequest()->addFilter(new \Bitrix\Main\Web\PostDecodeFilter);

        $ajaxParamsString = (new \Bitrix\Main\Security\Sign\Signer)->unsign(
            $ajax->getRequest()->get('SIGNED_RESULTS'),
            'drawwwr.edit'
        );

        $element = unserialize(base64_decode($ajaxParamsString));

        $drawwwrInstance = $ajax->getDrawwwrInstance();

        $drawwwrInstance->getDrawwwrElement()->setId((int)$element["ID"]);

        $drawwwrInstance->getIblockElement()->setId((int)$element["ELEMENT_ID"]);

        $drawwwrInstance->getImage()->setId((int)$element["IMAGE_FILE_ID"]);

        $drawwwrInstance->getH5i()->setId((int)$element["H5I_FILE_ID"]);
    } catch (\Bitrix\Main\Security\Sign\BadSignatureException $e) {
        $ajax->getResponse()->flush(\Bitrix\Main\Web\Json::encode([
            "status" => 'error',
            "code"   => 'BAD_SIGN',
            "text"   => ''
        ]));

        die();
    }

    if (!$ajax->checkUpdateStatus()) {
        die();
    }

    $ajax->sendSuccessResponse();
}

die();
