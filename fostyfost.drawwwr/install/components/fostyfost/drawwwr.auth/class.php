<?php

namespace FostyFost\Drawwwr\Components;

use \Bitrix\Main;
use \Bitrix\Main\Localization\Loc;
use FostyFost\Drawwwr\AccessController;
use \FostyFost\Drawwwr\BaseComponent;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

if (!Main\Loader::includeModule(DRAWWWR_MODULE_ID)) {
    return;
}

/**
 * Class DrawwwrAuthComponent
 *
 * @package FostyFost\Drawwwr\Components
 * @property array $arParams
 * @property array $arResult
 */
class DrawwwrAuthComponent extends BaseComponent
{
    /** @var bool $secureAuth */
    protected $security = false;

    /** @var array|bool $element */
    protected $element;

    public function onIncludeComponentLang()
    {
        $this->includeComponentLang(basename(__FILE__));

        Loc::loadMessages(__FILE__);
    }

    /**
     * @param $params
     *
     * @return array
     */
    public function onPrepareComponentParams($params)
    {
        $params["FORM_TYPE"] = $params["FORM_TYPE"] !== 'PASSWORD' ? 'ERROR' : 'PASSWORD';

        $params["MESSAGE"] = trim($params["MESSAGE"]);

        if (empty($params["MESSAGE"])) {
            if ($params["FORM_TYPE"] === 'PASSWORD') {
                $params["MESSAGE"] = '';
            } else {
                $params["MESSAGE"] = Loc::getMessage('FF_ERROR_MESSAGE');
            }
        }

        return [
            "FORM_TYPE"   => $params["FORM_TYPE"],
            "MESSAGE"     => $params["MESSAGE"],
            "AJAX_PARAMS" => $params["AJAX_PARAMS"]
        ];
    }

    protected function setSecurity()
    {
        $sec = new \CRsaSecurity();

        if ($arKeys = $sec->LoadKeys()) {
            $sec->SetKeys($arKeys);

            $sec->AddToForm('ff_auth_form', ["DRAWWWR_PASSWORD"]);

            $this->security = true;
        }
    }

    /**
     * @return bool|mixed|string
     * @throws \Bitrix\Main\ArgumentTypeException
     * @throws \Bitrix\Main\Security\Sign\BadSignatureException
     */
    protected function getEncryptedFile()
    {
        $content = '';

        try {
            $ajaxParamsString = (new Main\Security\Sign\Signer)->unsign(
                $this->arParams["AJAX_PARAMS"],
                'drawwwr.auth'
            );

            $this->element = unserialize(base64_decode($ajaxParamsString));

            $file = \CFile::GetFileArray($this->element["H5I_FILE_ID"]);

            if (!empty($file["SRC"])) {
                try {
                    $content = Main\IO\File::getFileContents(Main\Application::getDocumentRoot() . $file["SRC"]);
                } catch (\Exception $e) {
                    AccessController::showMessage();
                }
            }
        } catch (\Exception $e) {
            \LocalRedirect('/');
        }

        return $content;
    }

    /**
     * @throws \Bitrix\Main\ArgumentTypeException
     * @throws \Bitrix\Main\Security\Sign\BadSignatureException
     */
    protected function prepareData()
    {
        $this->arResult["SECURITY"] = $this->security;

        $this->arResult["ENCRYPTED_H5I"] = $this->getEncryptedFile();

        $this->arResult["ELEMENT"] = $this->element;
    }

    /**
     * @throws \Bitrix\Main\ArgumentTypeException
     * @throws \Bitrix\Main\Security\Sign\BadSignatureException
     */
    protected function prepareComponent()
    {
        $this->setSecurity();

        $this->prepareData();

        $this->includeComponentTemplate();
    }
}
