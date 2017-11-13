<?php

namespace FostyFost\Drawwwr;

/**
 * Class H5I
 *
 * @package FostyFost\Drawwwr
 */
class H5I implements Interfaces\Checkable, Interfaces\Element
{
    /**
     * @var string
     */
    private $data = '';

    /** @var int $id */
    private $id = 0;

    public function __construct($data)
    {
        $this->data = trim($data);
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->data);
    }

    /**
     * @param int $size
     *
     * @return bool
     */
    public function checkLength($size = 1048576)
    {
        return strlen($this->data) > $size;
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
     */
    public function create()
    {
        if (empty($this->data)) {
            return false;
        }

        $imageFileData = str_replace(' ', '', $this->data);

        $h5iFileId = \CFile::SaveFile(
            [
                "name"      => 'image.h5i',
                "type"      => 'application/octet-stream',
                "content"   => $imageFileData,
                "MODULE_ID" => DRAWWWR_MODULE_ID
            ],
            DRAWWWR_TYPE,
            true
        );

        $this->setId($h5iFileId);

        $this->cleanUp();

        return $this->id > 0;
    }

    /**
     * @return bool
     */
    public function update()
    {
        if ($this->id <= 0) {
            return false;
        }

        $imageFileData = str_replace(' ', '', $this->data);

        $h5iFileId = \CFile::SaveFile(
            [
                "name"      => 'image.h5i',
                "type"      => 'application/octet-stream',
                "content"   => $imageFileData,
                "MODULE_ID" => DRAWWWR_MODULE_ID
            ],
            DRAWWWR_TYPE,
            true
        );

        if ($h5iFileId > 0) {
            // Установим идентификатор нового файла
            $this->setId($h5iFileId);
        }

        return $h5iFileId > 0;
    }

    public function remove()
    {
        if ($this->id > 0) {
            \CFile::Delete($this->id);
        }
    }

    private function cleanUp()
    {
        // Если есть ID, то удалять файлы нет необходимости
        if ($this->id > 0) {
            return;
        }

        $imageId = Drawwwr::getInstance()->getImage()->getId();

        if ($imageId > 0) {
            \CFile::Delete($imageId);
        }
    }
}
