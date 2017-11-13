<?php

namespace FostyFost\Drawwwr;

use \Bitrix\Main;

/**
 * Class IblockElement
 *
 * @package FostyFost\Drawwwr
 */
class IblockElement implements Interfaces\Element
{
    /** @var int $id */
    private $id = 0;

    /**
     * IblockElement constructor
     */
    public function __construct()
    {
    }

    /**
     * @param $id
     */
    public function setId($id)
    {
        $this->id = (int)$id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return bool
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     * @throws \Bitrix\Main\LoaderException
     */
    public function create()
    {
        $drawwwrInstance = Drawwwr::getInstance();

        $elementId = 0;

        $imageFileId = $drawwwrInstance->getImage()->getId();

        $h5iFileId = $drawwwrInstance->getH5i()->getId();

        if ($imageFileId > 0 && $h5iFileId > 0 && Main\Loader::includeModule('iblock')) {
            $hash = md5("{$imageFileId}::{$h5iFileId}::" . rand());

            $elementId = (int)(new \CIBlockElement)->Add([
                "NAME"            => $hash,
                "CODE"            => $hash,
                "ACTIVE"          => 'Y',
                "IBLOCK_ID"       => (int)Main\Config\Option::get(
                    DRAWWWR_MODULE_ID,
                    'FF_DEFAULT_IBLOCK_ID'
                ),
                "PROPERTY_VALUES" => [
                    "IMAGE" => $imageFileId,
                    "H5I"   => $h5iFileId
                ]
            ]);
        }

        if ($elementId <= 0) {
            $drawwwrInstance->getImage()->remove();

            $drawwwrInstance->getH5i()->remove();
        } else {
            $this->setId($elementId);

            $drawwwrInstance->setIblockElement($this);
        }

        return $this->id > 0;
    }

    /**
     * @return bool
     * @throws \Bitrix\Main\LoaderException
     */
    public function update()
    {
        if ($this->id <= 0) {
            return false;
        }

        $updateResult = false;

        $drawwwrInstance = Drawwwr::getInstance();

        $imageFileId = $drawwwrInstance->getImage()->getId();

        $h5iFileId = $drawwwrInstance->getH5i()->getId();

        if ($imageFileId > 0 && $h5iFileId > 0 && Main\Loader::includeModule('iblock')) {
            $updateResult = (new \CIBlockElement)->Update(
                $this->id,
                [
                    "PROPERTY_VALUES" => [
                        "IMAGE" => $imageFileId,
                        "H5I"   => $h5iFileId
                    ]
                ]
            );
        }

        return $updateResult;
    }

    /**
     * @throws \Bitrix\Main\LoaderException
     */
    public function remove()
    {
        if ($this->id > 0 && Main\Loader::includeModule('iblock')) {
            \CIBlockElement::Delete($this->id);
        }
    }
}