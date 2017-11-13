<?php

namespace FostyFost\Drawwwr;

/**
 * Class Image
 *
 * @package FostyFost\Drawwwr
 */
class Image implements Interfaces\Checkable, Interfaces\Element
{
    /** @var string $data */
    private $data = '';

    /** @var int $id */
    private $id = 0;

    /**
     * Image constructor.
     *
     * @param $data
     */
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

        $imageFileData = str_replace(' ', '+', $this->data);

        $imageFileData = substr($imageFileData, strpos($imageFileData, ','));

        $imageFileData = base64_decode($imageFileData);

        $imageFileId = \CFile::SaveFile(
            [
                "name"      => 'image.png',
                "type"      => 'image/png',
                "content"   => $imageFileData,
                "MODULE_ID" => DRAWWWR_MODULE_ID
            ],
            DRAWWWR_TYPE,
            true
        );

        $this->setId($imageFileId);

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

        $imageFileData = str_replace(' ', '+', $this->data);

        $imageFileData = substr($imageFileData, strpos($imageFileData, ','));

        $imageFileData = base64_decode($imageFileData);

        $imageFileId = \CFile::SaveFile(
            [
                "name"      => 'image.png',
                "type"      => 'image/png',
                "content"   => $imageFileData,
                "MODULE_ID" => DRAWWWR_MODULE_ID
            ],
            DRAWWWR_TYPE,
            true
        );

        if ($imageFileId > 0) {
            // Установим идентификатор нового файла
            $this->setId($imageFileId);
        }

        return $imageFileId > 0;
    }

    public function remove()
    {
        if ($this->id > 0) {
            \CFile::Delete($this->id);
        }
    }
}
