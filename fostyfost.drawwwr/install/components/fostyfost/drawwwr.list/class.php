<?php

namespace FostyFost\Drawwwr\Components;

use \Bitrix\Main\Loader;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\ArgumentNullException;
use \Bitrix\Main\UI\PageNavigation;
use \FostyFost\Drawwwr;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

if (!Loader::includeModule(DRAWWWR_MODULE_ID)) {
    return;
}

/**
 * Class DrawwwrListComponent
 *
 * @package FostyFost\Drawwwr\Components
 * @property array $arParams
 * @property array $arResult
 */
class DrawwwrListComponent extends Drawwwr\BaseComponent
{
    use Drawwwr\Traits\Common;

    /**
     * Массив размеров изображений
     */
    const FF_FILE_SIZES = [
        "375"   => [
            "width"  => 375,
            "height" => 234
        ],
        "480"   => [
            "width"  => 480,
            "height" => 300
        ],
        "800"   => [
            "width"  => 800,
            "height" => 500
        ],
        "big"   => [
            "width"  => 1600,
            "height" => 1000
        ],
        "thumb" => [
            "width"  => 180,
            "height" => 126
        ]
    ];

    /**
     * Массив фильтров для изображений
     */
    const FF_FILE_FILTERS = [
        [
            "name"      => 'sharpen',
            "precision" => 0
        ]
    ];

    /**
     * Кешируемые ключи arResult
     *
     * @var array $cacheKeys
     */
    protected $cacheKeys = [];

    /**
     * Дополнительные параметры, от которых должен зависеть кеш
     *
     * @var array $cacheExtras
     */
    protected $cacheExtras = [];

    /**
     * Постарничная навигация
     *
     * @var \Bitrix\Main\UI\PageNavigation $nav
     */
    protected $nav;

    /**
     * Флаг использования постраничной навигации
     *
     * @var bool $useNav
     */
    protected $useNav = false;

    /**
     * Метод подготавливает входные параметры
     *
     * @param $params
     *
     * @return array
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     */
    public function onPrepareComponentParams($params)
    {
        $params["COUNT"] = (int)$params["COUNT"];

        $params["EDIT_URL"] = trim($params["EDIT_URL"]);

        $params["CACHE_TIME"] = (int)$params["CACHE_TIME"];

        return [
            "IBLOCK_TYPE"    => DRAWWWR_TYPE,
            "IBLOCK_ID"      => (int)Option::get(DRAWWWR_MODULE_ID, 'FF_DEFAULT_IBLOCK_ID'),
            "COUNT"          => $params["COUNT"] > 0 ? $params["COUNT"] : 10,
            "PROPERTY_IMAGE" => (int)Option::get(DRAWWWR_MODULE_ID, 'FF_DEFAULT_PROPERTY_IMAGE'),
            "EDIT_URL"       => !empty($params["EDIT_URL"]) ? $params["EDIT_URL"] : '',
            "CACHE_TIME"     => $params["CACHE_TIME"] > 0 ? $params["CACHE_TIME"] : 3600
        ];
    }

    /**
     * Метод проверяет заполнение обязательных параметров
     *
     * @throws \Bitrix\Main\ArgumentNullException
     */
    protected function checkParams()
    {
        if (empty($this->arParams["IBLOCK_ID"])) {
            throw new ArgumentNullException('IBLOCK_ID');
        }

        if (empty($this->arParams["PROPERTY_IMAGE"])) {
            throw new ArgumentNullException('PROPERTY_IMAGE');
        }
    }

    /**
     * Метод определяет, читать данные из кеша или нет
     *
     * @return bool
     */
    protected function readDataFromCache()
    {
        if ($this->arParams["CACHE_TYPE"] === 'N') {
            return false;
        }

        return !($this->startResultCache(
            false,
            $this->cacheExtras,
            '/drawwwr/' . md5(serialize($this->arParams))
        ));
    }

    /**
     * Метод кеширует ключи массива arResult
     */
    protected function putDataToCache()
    {
        if (is_array($this->cacheKeys) && sizeof($this->cacheKeys) > 0) {
            $this->setResultCacheKeys($this->cacheKeys);
        }
    }

    /**
     * Метод прерывает кеширование
     */
    protected function abortDataCache()
    {
        $this->abortResultCache();
    }

    /**
     * Метод определяет параметры постраничной навигации
     */
    protected function preparePageNavigation()
    {
        if ($this->arParams["COUNT"] > 0) {
            $this->nav = new PageNavigation(DRAWWWR_TYPE);

            $this->nav->allowAllRecords(false)->setPageSize($this->arParams["COUNT"])->initFromUri();

            $this->nav->setRecordCount(Drawwwr\Model\DrawwwrFilesTable::getCount());

            $this->cacheExtras["OFFSET"] = $this->nav->getOffset();

            $this->cacheExtras["LIMIT"] = $this->nav->getLimit();

            $this->cacheExtras["PAGE_COUNT"] = $this->nav->getPageCount();

            $this->cacheExtras["PAGE_SIZE"] = $this->nav->getPageSize();

            $this->cacheExtras["RECORD_COUNT"] = $this->nav->getRecordCount();

            $this->useNav = true;
        }
    }

    /**
     * Метод получения результатов
     *
     * @throws \Bitrix\Main\ArgumentException
     */
    protected function getResult()
    {
        $elements = Drawwwr\Model\DrawwwrFilesTable::getList([
            "offset" => $this->useNav ? $this->nav->getOffset() : 0,
            "limit"  => $this->useNav ? $this->nav->getLimit() : $this->arParams["COUNT"],
            "order"  => ["ID" => 'DESC'],
            "select" => [
                "ID",
                "CAN_EDIT",
                "IMAGE_FILE_ID",
                "H5I_FILE_ID",
                "ELEMENT_CODE" => 'ELEMENT.CODE'
            ]
        ])->fetchAll();

        foreach ($elements as $item) {
            if (
                empty($item["IMAGE_FILE_ID"])
                || empty($item["ELEMENT_CODE"])
            ) {
                continue;
            }

            if ($item["CAN_EDIT"] === 'Y') {
                $item["EDIT_PAGE_URL"] = preg_replace(
                    "'(?<!:)/+'s", '/',
                    str_replace('#ELEMENT_CODE#', $item["ELEMENT_CODE"], $this->arParams["EDIT_URL"])
                );
            }

            $item["IMAGES"] = $this->getResizeFilesArray($item["IMAGE_FILE_ID"]);

            $this->arResult["ITEMS"][] = $item;
        }

        $this->cacheKeys = ["ITEMS"];

        unset($elements);

        if ($this->useNav) {
            $this->arResult["NAV_OBJECT"] = $this->nav;
        }
    }

    /**
     * Метод для подготовки изображений различных размеров
     *
     * @param $fileId
     *
     * @return array
     */
    protected function getResizeFilesArray($fileId)
    {
        $result = ["responsive" => ''];

        $comma = '';

        if ((int)$fileId <= 0) {
            return $result;
        }

        foreach (self::FF_FILE_SIZES as $sizeKey => $arSize) {
            $result[$sizeKey] = \CFile::ResizeImageGet(
                $fileId,
                $arSize,
                BX_RESIZE_IMAGE_EXACT,
                true,
                self::FF_FILE_FILTERS
            );

            if ((int)$sizeKey > 0) {
                $result["responsive"] .= "{$comma}{$result[$sizeKey]["src"]} {$sizeKey}";

                $comma = ', ';
            }
        }

        return $result;
    }

    /**
     * @throws \Bitrix\Main\ArgumentException
     */
    protected function prepareData()
    {
        if (!$this->readDataFromCache()) {
            $this->getResult();

            $this->putDataToCache();

            $this->endResultCache();
        }
    }

    /**
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\LoaderException
     */
    protected function prepareComponent()
    {
        $this->checkModules(["iblock"]);

        $this->checkParams();

        $this->preparePageNavigation();

        $this->prepareData();

        $this->includeComponentTemplate();
    }
}
