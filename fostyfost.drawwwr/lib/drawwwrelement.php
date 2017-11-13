<?php

namespace FostyFost\Drawwwr;

/**
 * Class DrawwwrElement
 *
 * @package FostyFost\Drawwwr
 */
class DrawwwrElement implements Interfaces\Element
{
    /** @var int */
    private $id = 0;

    /**
     * DrawwwrElement constructor
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
     * @throws \Bitrix\Main\LoaderException
     * @throws \Exception
     */
    public function create()
    {
        $drawwwrInstance = Drawwwr::getInstance();

        $imageFileId = $drawwwrInstance->getImage()->getId();

        $h5iFileId = $drawwwrInstance->getH5i()->getId();

        $elementId = $drawwwrInstance->getIblockElement()->getId();

        if ($imageFileId > 0 && $h5iFileId > 0 && $elementId > 0) {
            $password = $drawwwrInstance->getPassword()->generateHash();

            /** @var \Bitrix\Main\Entity\AddResult $drawwwrElementAddingResult */
            $drawwwrElementAddingResult = Model\DrawwwrFilesTable::add([
                "IMAGE_FILE_ID"     => $imageFileId,
                "H5I_FILE_ID"       => $h5iFileId,
                "IBLOCK_ELEMENT_ID" => $elementId,
                "PASSWORD"          => $password,
                "CAN_EDIT"          => 'Y'
            ]);

            if (!$drawwwrElementAddingResult->isSuccess()) {
                $drawwwrInstance->getImage()->remove();

                $drawwwrInstance->getH5i()->remove();

                $drawwwrInstance->getIblockElement()->remove();
            } else {
                $this->setId($drawwwrElementAddingResult->getId());

                $drawwwrInstance->setDrawwwrElement($this);
            }
        }

        return $this->id > 0;
    }

    /**
     * @return bool
     * @throws \Bitrix\Main\LoaderException
     * @throws \Exception
     */
    public function update()
    {
        if ($this->id <= 0) {
            return false;
        }

        $drawwwrInstance = Drawwwr::getInstance();

        $oldImageFileId = $drawwwrInstance->getImage()->getId();

        // Если файл не сохранён, то вернём объекту старый идентификатор
        if (!$drawwwrInstance->getImage()->update()) {
            $drawwwrInstance->getImage()->setId($oldImageFileId);

            return false;
        }

        $newImageFileId = $drawwwrInstance->getImage()->getId();

        $oldH5iFileId = $drawwwrInstance->getH5i()->getId();

        // Если файл не сохранён, то вернём объекту старый идентификатор
        // и удалим файл новый файл изображения
        if (!$drawwwrInstance->getH5i()->update()) {
            $drawwwrInstance->getImage()->remove();

            $drawwwrInstance->getImage()->setId($oldImageFileId);

            $drawwwrInstance->getH5i()->setId($oldH5iFileId);

            return false;
        }

        $newH5iFileId = $drawwwrInstance->getH5i()->getId();

        // Если не удалось обновить свойства элемента инфоблока,
        // то удалим все новые файлы и вернем их объектам прежние идентификаторы
        if (!$drawwwrInstance->getIblockElement()->update()) {
            $drawwwrInstance->getImage()->remove();

            $drawwwrInstance->getImage()->setId($oldImageFileId);

            $drawwwrInstance->getH5i()->remove();

            $drawwwrInstance->getH5i()->setId($oldH5iFileId);
        }

        $password = $drawwwrInstance->getPassword()->generateHash();

        /** @var \Bitrix\Main\Entity\UpdateResult $drawwwrElementUpdatingResult */
        $drawwwrElementUpdatingResult = Model\DrawwwrFilesTable::update(
            $this->id,
            [
                "IMAGE_FILE_ID" => $newImageFileId,
                "H5I_FILE_ID"   => $newH5iFileId,
                "PASSWORD"      => $password,
                "CAN_EDIT"      => 'Y'
            ]
        );

        // Если текущую сущность не удалось обновить,
        // то удалим все новые файлы и вернём свойства
        // и попытаемся вернуть свойства элемента инфоблока в прежнее состояние
        if (!$drawwwrElementUpdatingResult->isSuccess()) {
            $drawwwrInstance->getImage()->remove();

            $drawwwrInstance->getImage()->setId($oldImageFileId);

            $drawwwrInstance->getH5i()->remove();

            $drawwwrInstance->getH5i()->setId($oldH5iFileId);

            $drawwwrInstance->getIblockElement()->update();
        } else {
            AccessController::logOut();

            \BXClearCache(true, '/drawwwr/');
        }

        return $drawwwrElementUpdatingResult->isSuccess();
    }

    /**
     * @throws \Exception
     */
    public function remove()
    {
        if ($this->id > 0) {
            Model\DrawwwrFilesTable::delete($this->id);
        }
    }

    /**
     * @return bool
     * @throws \Bitrix\Main\ArgumentException
     */
    public function isEditable()
    {
        return AccessController::isEditable($this->id);
    }
}