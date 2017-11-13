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
class DrawwwrCreateComponent extends Drawwwr\BaseComponent
{
    use Drawwwr\Traits\Common;

    /** @var bool $secureAuth */
    protected $security = false;

    /**
     * @param $params
     *
     * @return array
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     */
    public function onPrepareComponentParams($params)
    {
        return [
            "IBLOCK_TYPE"      => DRAWWWR_TYPE,
            "IBLOCK_ID"        => (int)Main\Config\Option::get(
                DRAWWWR_MODULE_ID,
                'FF_DEFAULT_IBLOCK_ID'
            ),
            "ELEMENT_CODE"     => trim($params["ELEMENT_CODE"]),
            "USE_COLOR_PICKER" => $params["USE_COLOR_PICKER"] === 'Y' ? 'Y' : 'N'
        ];
    }

    protected function setSecurity()
    {
        $sec = new \CRsaSecurity();

        if ($arKeys = $sec->LoadKeys()) {
            $sec->SetKeys($arKeys);

            $sec->AddToForm('creationForm', ["DRAWWWR_PASSWORD"]);

            $this->security = true;
        }
    }

    protected function prepareData()
    {
        $this->arResult["SECURITY"] = $this->security;
    }

    protected function prepareComponent()
    {
        $this->checkModules(['iblock']);

        $this->checkPanel();

        $this->checkIncludeAreas();

        $this->setSecurity();

        $this->prepareData();

        $this->includeComponentTemplate();
    }
}
