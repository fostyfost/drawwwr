<?php

namespace FostyFost\Drawwwr\Components;

use \Bitrix\Main;
use \FostyFost\Drawwwr;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

if (!Main\Loader::includeModule(DRAWWWR_MODULE_ID)) {
    return;
}

/**
 * Class DrawwwrCreateComponent
 *
 * @package FostyFost\Drawwwr\Components
 * @property array $arParams
 * @property array $arResult
 */
class DrawwwrEditComponent extends Drawwwr\BaseComponent
{
    use Drawwwr\Traits\Common;

    /** @var array|bool $element */
    protected $element;

    /** @var bool $showError */
    protected $showError = false;

    /**
     * @param $params
     *
     * @return array
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     */
    public function onPrepareComponentParams($params)
    {
        $params["ELEMENT_CODE"] = trim($params["ELEMENT_CODE"]);

        $params["EDIT_URL"] = trim($params["EDIT_URL"]);

        return [
            "IBLOCK_TYPE"      => DRAWWWR_TYPE,
            "IBLOCK_ID"        => (int)Main\Config\Option::get(
                DRAWWWR_MODULE_ID,
                'FF_DEFAULT_IBLOCK_ID'
            ),
            "ELEMENT_CODE"     => !empty($params["ELEMENT_CODE"]) ? $params["ELEMENT_CODE"] : '',
            "EDIT_URL"         => !empty($params["EDIT_URL"]) ? $params["EDIT_URL"] : '',
            "USE_COLOR_PICKER" => $params["USE_COLOR_PICKER"] === 'Y' ? 'Y' : 'N'
        ];
    }

    /**
     * @throws \Exception
     */
    protected function checkElementCodeParam()
    {
        if (empty($this->arParams["ELEMENT_CODE"])) {
            $this->return404();
        }
    }

    /**
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     */
    protected function getElementData()
    {
        $this->element = Drawwwr\Model\DrawwwrFilesTable::getList([
            "filter" => [
                "=ELEMENT.IBLOCK_ID" => Main\Config\Option::get(
                    DRAWWWR_MODULE_ID,
                    'FF_DEFAULT_IBLOCK_ID'
                ),
                "=ELEMENT.CODE"      => $this->arParams["ELEMENT_CODE"]
            ],
            "select" => [
                "ID",
                "ELEMENT_ID"   => 'ELEMENT.ID',
                "ELEMENT_CODE" => 'ELEMENT.CODE',
                "CAN_EDIT",
                "H5I_FILE_ID",
                "IMAGE_FILE_ID"
            ],
            "limit"  => 1
        ])->fetch();
    }

    /**
     * @throws \Exception
     */
    protected function checkElementData()
    {
        if (empty($this->element) || empty($this->element["ELEMENT_CODE"]) || empty($this->element["ID"])) {
            $this->return404();
        }
    }

    protected function checkAuthor()
    {
        /**
         * @var \Bitrix\Main\Event $event
         * @see \FostyFost\Drawwwr\Events::onBeforeEdit
         */
        $event = new Main\Event(
            DRAWWWR_MODULE_ID,
            'onBeforeEdit',
            [
                "ID"            => $this->element["ID"],
                "ELEMENT_CODE"  => $this->element["ELEMENT_CODE"],
                "CAN_EDIT"      => $this->element["CAN_EDIT"],
                "H5I_FILE_ID"   => $this->element["H5I_FILE_ID"],
                "IMAGE_FILE_ID" => $this->element["IMAGE_FILE_ID"]
            ]
        );

        $event->send();

        /**
         * @var \Bitrix\Main\EventResult[] $result
         * @see \FostyFost\Drawwwr\Events::onBeforeEdit
         */
        $eventResults = $event->getResults();

        /** @var \Bitrix\Main\EventResult $result */
        foreach ($eventResults as $result) {
            if ($result->getType() !== Main\EventResult::SUCCESS) {
                $this->showError = true;

                break;
            }
        }
    }

    protected function checkError()
    {
        if ($this->showError) {
            Drawwwr\AccessController::showMessage();
        }
    }

    protected function setSecurity()
    {
        $sec = new \CRsaSecurity();

        if ($arKeys = $sec->LoadKeys()) {
            $sec->SetKeys($arKeys);

            $sec->AddToForm('editForm', ["DRAWWWR_PASSWORD"]);

            $this->security = true;
        }
    }

    protected function prepareData()
    {
        $this->element["EDIT_PAGE_URL"] = preg_replace(
            "'(?<!:)/+'s", '/',
            str_replace('#ELEMENT_CODE#', $this->arParams["ELEMENT_CODE"], $this->arParams["EDIT_URL"])
        );

        $this->arResult = $this->element;

        $this->arResult["SIGNED_RESULTS"] = (new Main\Security\Sign\Signer)->sign(
            base64_encode(serialize($this->element)),
            'drawwwr.edit'
        );
    }

    protected function prepareComponent()
    {
        $this->checkModules(['iblock']);

        $this->checkPanel();

        $this->checkIncludeAreas();

        $this->checkElementCodeParam();

        $this->getElementData();

        $this->checkElementData();

        $this->checkAuthor();

        $this->checkError();

        $this->setSecurity();

        $this->prepareData();

        $this->includeComponentTemplate();
    }
}
