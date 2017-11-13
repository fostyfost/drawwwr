<?php

namespace FostyFost\Drawwwr\Helpers;

use \Bitrix\Main;
use \Bitrix\Main\Localization\Loc;
use \FostyFost\Drawwwr;

Loc::loadMessages(__FILE__);

/**
 * Class AjaxHelper
 *
 * @package FostyFost\Drawwwr\Helpers
 */
class AjaxHelper
{
    /** @var \Bitrix\Main\Context $context */
    private $context;

    /** @var \Bitrix\Main\HttpResponse $response */
    private $response;

    /** @var \Bitrix\Main\HttpRequest $request */
    private $request;

    /** @var \FostyFost\Drawwwr\Drawwwr $drawwwrInstance */
    private $drawwwrInstance;

    /**
     * AjaxHelper constructor
     *
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     * @throws \Bitrix\Main\SystemException
     */
    public function __construct()
    {
        $this->context = Main\Application::getInstance()->getContext();

        $this->response = new Main\HttpResponse($this->context);

        $this->response->addHeader('Content-Type', 'application/json');

        $this->request = $this->context->getRequest();

        $this->drawwwrInstance = Drawwwr\Drawwwr::getInstance();
    }

    /**
     * @return \Bitrix\Main\HttpRequest
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return \Bitrix\Main\HttpResponse
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return \FostyFost\Drawwwr\Drawwwr
     */
    public function getDrawwwrInstance()
    {
        return $this->drawwwrInstance;
    }

    /**
     * @return bool
     */
    public function isAuth()
    {
        return $this->request->get('AUTH') === 'Y';
    }

    /**
     * @return bool
     */
    public function isCreate()
    {
        return $this->request->get('CREATE') === 'Y';
    }

    /**
     * @return bool
     */
    public function isUpdate()
    {
        return $this->request->get('UPDATE') === 'Y';
    }

    /**
     * @return bool
     */
    public function isDelete()
    {
        return $this->request->get('DELETE') === 'Y';
    }

    /**
     * @return bool
     */
    public function isLogOut()
    {
        return $this->request->get('LOGOUT') === 'Y';
    }

    public function createIblockObject()
    {
        $this->drawwwrInstance->setIblockElement(new Drawwwr\IblockElement());
    }

    public function createDrawwwrObject()
    {
        $this->drawwwrInstance->setDrawwwrElement(new Drawwwr\DrawwwrElement());
    }

    public function setH5i()
    {
        $this->drawwwrInstance->setH5i(new Drawwwr\H5I($this->request->get('H5I_FILE')));
    }

    public function setImage()
    {
        $this->drawwwrInstance->setImage(new Drawwwr\Image($this->request->get('IMAGE_FILE')));
    }

    /**
     * @param bool $fromGlobals
     */
    public function setPassword($fromGlobals = false)
    {
        $this->drawwwrInstance->setPassword(
            $fromGlobals
                ? new Drawwwr\Password($_POST["DRAWWWR_PASSWORD"])
                : new Drawwwr\Password($this->request->get('DRAWWWR_PASSWORD'))
        );
    }

    /**
     * @return bool
     * @throws \Bitrix\Main\ArgumentException
     */
    public function checkIblockModule()
    {
        if (!Main\ModuleManager::isModuleInstalled('iblock')) {
            $this->response->flush(Main\Web\Json::encode([
                "status" => 'error',
                "code"   => 'IBLOCK_MODULE_NOT_INSTALLED',
                "text"   => Loc::getMessage('FF_IBLOCK_MODULE_NOT_INSTALLED')
            ]));

            return false;
        }

        return true;
    }

    /**
     * @return bool
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     */
    public function checkDrawwwrModule()
    {
        if (!Drawwwr\Drawwwr::isInstalled()) {
            $this->response->flush(Main\Web\Json::encode([
                "status" => 'error',
                "code"   => 'DRAWWWR_MODULE_NOT_INSTALLED',
                "text"   => Loc::getMessage('FF_DRAWWWR_MODULE_NOT_INSTALLED')
            ]));

            return false;
        }

        return true;
    }

    /**
     * @return bool
     * @throws \Bitrix\Main\ArgumentException
     */
    public function checkAjax()
    {
        if (!$this->request->isAjaxRequest()) {
            $this->response->flush(Main\Web\Json::encode([
                "status" => 'error',
                "code"   => 'NOT_XHR',
                "text"   => Loc::getMessage('FF_NOT_XHR')
            ]));

            return false;
        }

        return true;
    }

    /**
     * @return bool
     * @throws \Bitrix\Main\ArgumentException
     */
    public function checkPost()
    {
        if (!$this->request->isPost()) {
            $this->response->flush(Main\Web\Json::encode([
                "status" => 'error',
                "code"   => 'NOT_POST',
                "text"   => Loc::getMessage('FF_NOT_FOST')
            ]));

            return false;
        }

        return true;
    }

    /**
     * @return bool
     * @throws \Bitrix\Main\ArgumentException
     */
    public function checkSession()
    {
        if (!\check_bitrix_sessid()) {
            $this->response->flush(Main\Web\Json::encode([
                "status" => 'error',
                "code"   => 'INVALID_SESSION',
                "text"   => Loc::getMessage('FF_INVALID_SESSION')
            ]));

            return false;
        }

        return true;
    }

    /**
     * @return bool
     * @throws \Bitrix\Main\ArgumentException
     */
    public function checkH5iIsEmpty()
    {
        $result = true;

        $h5i = $this->drawwwrInstance->getH5i();

        if (!$h5i instanceof Drawwwr\H5I || $h5i->isEmpty()) {
            $this->response->flush(Main\Web\Json::encode([
                "status" => 'error',
                "code"   => 'NO_H5I',
                "text"   => Loc::getMessage('FF_NO_H5I')
            ]));

            $result = false;
        }

        unset($h5i);

        return $result;
    }

    /**
     * @return bool
     * @throws \Bitrix\Main\ArgumentException
     */
    public function checkImageIsEmpty()
    {
        $result = true;

        $image = $this->drawwwrInstance->getImage();

        if (!$image instanceof Drawwwr\Image || $image->isEmpty()) {
            $this->response->flush(Main\Web\Json::encode([
                "status" => 'error',
                "code"   => 'NO_IMAGE',
                "text"   => Loc::getMessage('FF_NO_IMAGE')
            ]));

            $result = false;
        }

        unset($image);

        return $result;
    }

    /**
     * @param int $size
     *
     * @return bool
     * @throws \Bitrix\Main\ArgumentException
     */
    public function checkH5iLength($size = 1048576)
    {
        $result = true;

        $h5i = $this->drawwwrInstance->getH5i();

        if (!$h5i instanceof Drawwwr\H5I || $h5i->checkLength($size)) {
            $this->response->flush(Main\Web\Json::encode([
                "status" => 'error',
                "code"   => 'H5I_FILE_SIZE_ERROR',
                "text"   => Loc::getMessage('FF_H5I_FILE_SIZE_ERROR')
            ]));

            $result = false;
        }

        unset($h5i);

        return $result;
    }

    /**
     * @param int $size
     *
     * @return bool
     * @throws \Bitrix\Main\ArgumentException
     */
    public function checkImageLength($size = 1048576)
    {
        $result = true;

        $image = $this->drawwwrInstance->getImage();

        if (!$image instanceof Drawwwr\Image || $image->checkLength($size)) {
            $this->response->flush(Main\Web\Json::encode([
                "status" => 'error',
                "code"   => 'IMAGE_FILE_SIZE_ERROR',
                "text"   => Loc::getMessage('FF_IMAGE_FILE_SIZE_ERROR')
            ]));

            $result = false;
        }

        unset($image);

        return $result;
    }

    /**
     * @return bool
     * @throws \Bitrix\Main\ArgumentException
     */
    public function checkPasswordIsEmpty()
    {
        $result = true;

        $password = $this->drawwwrInstance->getPassword();

        if (!$password instanceof Drawwwr\Password || $password->isEmpty()) {
            $this->response->flush(Main\Web\Json::encode([
                "status" => 'error',
                "code"   => 'EMPTY_PASSWORD',
                "text"   => Loc::getMessage('FF_EMPTY_PASSWORD')
            ]));

            $result = false;
        }

        unset($password);

        return $result;
    }

    /**
     * @param int $size
     *
     * @return bool
     * @throws \Bitrix\Main\ArgumentException
     */
    public function checkPasswordMinLength($size = 4)
    {
        $result = true;

        $password = $this->drawwwrInstance->getPassword();

        if (!$password instanceof Drawwwr\Password || !$password->checkLength($size)) {
            $this->response->flush(Main\Web\Json::encode([
                "status" => 'error',
                "code"   => 'PASSWORD_MIN_LENGTH_ERROR',
                "text"   => Loc::getMessage('FF_PASSWORD_MIN_LENGTH_ERROR')
            ]));

            $result = false;
        }

        unset($password);

        return $result;
    }

    /**
     * @param int $size
     *
     * @return bool
     * @throws \Bitrix\Main\ArgumentException
     */
    public function checkPasswordMaxLength($size = 16)
    {
        $result = true;

        $password = $this->drawwwrInstance->getPassword();

        if (!$password instanceof Drawwwr\Password || $password->checkLength($size)) {
            $this->response->flush(Main\Web\Json::encode([
                "status" => 'error',
                "code"   => 'PASSWORD_MAX_LENGTH_ERROR',
                "text"   => Loc::getMessage('FF_PASSWORD_MAX_LENGTH_ERROR')
            ]));

            $result = false;
        }

        unset($password);

        return $result;
    }

    /**
     * @return bool
     * @throws \Bitrix\Main\ArgumentException
     */
    public function checkCreationImageFile()
    {
        $result = true;

        $image = $this->drawwwrInstance->getImage();

        if (!$image instanceof Drawwwr\Image || !$image->create()) {
            $this->response->flush(Main\Web\Json::encode([
                "status" => 'error',
                "code"   => 'IMAGE_CREATE_ERROR',
                "text"   => Loc::getMessage('FF_IMAGE_CREATE_ERROR')
            ]));

            $result = false;
        }

        unset($image);

        return $result;
    }

    /**
     * @return bool
     * @throws \Bitrix\Main\ArgumentException
     */
    public function checkCreationH5iFile()
    {
        $result = true;

        $h5i = $this->drawwwrInstance->getH5i();

        if (!$h5i instanceof Drawwwr\H5I || !$h5i->create()) {
            $this->response->flush(Main\Web\Json::encode([
                "status" => 'error',
                "code"   => 'H5I_CREATE_ERROR',
                "text"   => Loc::getMessage('FF_H5I_CREATE_ERROR')
            ]));

            $result = false;
        }

        unset($h5i);

        return $result;
    }

    /**
     * @return bool
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     * @throws \Bitrix\Main\LoaderException
     */
    public function checkCreationIblockElement()
    {
        $result = true;

        $element = $this->drawwwrInstance->getIblockElement();

        if (!$element instanceof Drawwwr\IblockElement || !$element->create()) {
            $this->response->flush(Main\Web\Json::encode([
                "status" => 'error',
                "code"   => 'ELEMENT_CREATE_ERROR',
                "text"   => Loc::getMessage('FF_ELEMENT_CREATE_ERROR')
            ]));

            $result = false;
        }

        unset($element);

        return $result;
    }

    /**
     * @return bool
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Exception
     */
    public function checkRegistrationDrawwwrElement()
    {
        $result = true;

        $element = $this->drawwwrInstance->getDrawwwrElement();

        if (!$element instanceof Drawwwr\DrawwwrElement || !$element->create()) {
            $this->response->flush(Main\Web\Json::encode([
                "status" => 'error',
                "code"   => 'H5I_CREATE_ERROR',
                "text"   => Loc::getMessage('FF_H5I_CREATE_ERROR')
            ]));

            $result = false;
        }

        unset($element);

        return $result;
    }

    /**
     * @param $arParams
     *
     * @return bool
     * @throws \Bitrix\Main\ArgumentException
     */
    public function checkDecryptionStatus($arParams)
    {
        $sec = new \CRsaSecurity();

        $arKeys = $sec->LoadKeys();

        if ($arKeys) {
            $sec->SetKeys($arKeys);

            $errorNumber = $sec->AcceptFromForm($arParams);

            if ($errorNumber == \CRsaSecurity::ERROR_SESS_CHECK) {
                $this->response->flush(Main\Web\Json::encode([
                    "status" => 'error',
                    "code"   => 'SESSION_EXPIRED',
                    "text"   => Loc::getMessage('FF_SESSION_EXPIRED')
                ]));

                return false;
            } elseif ($errorNumber < 0) {
                $this->response->flush(Main\Web\Json::encode([
                    "status" => 'error',
                    "code"   => 'DECODE_ERROR',
                    "text"   => Loc::getMessage(
                        'FF_DECODE_ERROR',
                        ["#ERROR_CODE#" => $errorNumber]
                    )
                ]));

                return false;
            }
        }

        return true;
    }

    public function checkAjaxParams()
    {
        $result = true;

        try {
            $this->request->addFilter(new Main\Web\PostDecodeFilter);

            $ajaxParamsString = (new Main\Security\Sign\Signer)->unsign(
                $this->request->get('AJAX_PARAMS'),
                'drawwwr.auth'
            );

            $element = unserialize(base64_decode($ajaxParamsString));

            $this->drawwwrInstance->getDrawwwrElement()->setId((int)$element["ID"]);
        } catch (Main\Security\Sign\BadSignatureException $e) {
            $this->response->flush(Main\Web\Json::encode([
                "status" => 'error',
                "code"   => 'INVALID_ID',
                "text"   => Loc::getMessage('FF_INVALID_ID')
            ]));

            $result = false;
        }

        return $result;
    }

    /**
     * @return bool
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     */
    public function checkDrawwwrElementId()
    {
        $result = true;

        Main\Loader::includeModule(DRAWWWR_MODULE_ID);

        $element = $this->drawwwrInstance->getDrawwwrElement();

        if (!$element instanceof Drawwwr\DrawwwrElement && $element->getId() <= 0) {
            $this->response->flush(Main\Web\Json::encode([
                "status" => 'error',
                "code"   => 'INVALID_ID',
                "text"   => Loc::getMessage('FF_INVALID_ID')
            ]));

            $result = false;
        }

        unset($element);

        return $result;
    }

    /**
     * @return bool
     * @throws \Bitrix\Main\ArgumentException
     */
    public function checkCanEdit()
    {
        $result = true;

        $element = $this->drawwwrInstance->getDrawwwrElement();

        if (!$element instanceof Drawwwr\DrawwwrElement || $element->isEditable() !== 'Y') {
            $this->response->flush(Main\Web\Json::encode([
                "status" => 'error',
                "code"   => 'NOT_EDITABLE',
                "text"   => Loc::getMessage('FF_NOT_EDITABLE')
            ]));

            $result = false;
        }

        unset($element);

        return $result;
    }

    /**
     * @return bool
     * @throws \Bitrix\Main\ArgumentException
     */
    public function authorize()
    {
        $result = true;

        $element = $this->drawwwrInstance->getDrawwwrElement();

        if (
            !$element instanceof Drawwwr\DrawwwrElement
            || !Drawwwr\AccessController::logIn($element->getId())
        ) {
            $this->response->flush(Main\Web\Json::encode([
                "status" => 'error',
                "code"   => 'INVALID_PASSWORD',
                "text"   => Loc::getMessage('FF_INVALID_PASSWORD')
            ]));

            $result = false;
        }

        unset($element);

        return $result;
    }

    public function checkUpdateStatus()
    {
        $result = true;

        $element = $this->drawwwrInstance->getDrawwwrElement();

        if (
            !$element instanceof Drawwwr\DrawwwrElement
            || !$element->update()
        ) {
            $this->response->flush(Main\Web\Json::encode([
                "status" => 'error',
                "code"   => 'UPDATE_ERROR',
                "text"   => Loc::getMessage('FF_UPDATE_ERROR')
            ]));

            $result = false;
        }

        unset($element);

        return $result;
    }

    /**
     * @param string $status
     * @param string $code
     *
     * @throws \Bitrix\Main\ArgumentException
     */
    public function sendSuccessResponse($status = 'success', $code = 'SUCCESS')
    {
        $this->response->flush(Main\Web\Json::encode([
            "status" => $status,
            "code"   => $code,
            "text"   => Loc::getMessage("FF_{$code}")
        ]));
    }
}
