<?php

namespace FostyFost\Drawwwr;

use \Bitrix\Main;

/**
 * Class Drawwwr
 *
 * @package FostyFost\Drawwwr
 */
class Drawwwr
{
    /** @var \FostyFost\Drawwwr\Drawwwr $instance */
    protected static $instance = null;

    /** @var \FostyFost\Drawwwr\IblockElement */
    protected $iBlockElement;

    /** @var \FostyFost\Drawwwr\DrawwwrElement */
    private $drawwwrElement;

    /** @var \FostyFost\Drawwwr\Image $image */
    protected $image;

    /** @var \FostyFost\Drawwwr\H5I $h5i */
    protected $h5i;

    /** @var \FostyFost\Drawwwr\Password $password */
    protected $password;

    /**
     * Drawwwr constructor
     */
    protected function __construct()
    {
    }

    /**
     * @return \FostyFost\Drawwwr\Drawwwr
     */
    public static function getInstance()
    {
        if (!isset(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * @param \FostyFost\Drawwwr\IblockElement $iblockElement
     */
    public function setIblockElement(IblockElement $iblockElement)
    {
        $this->iBlockElement = $iblockElement;
    }

    /**
     * @return \FostyFost\Drawwwr\IblockElement
     */
    public function getIblockElement()
    {
        return $this->iBlockElement;
    }

    /**
     * @param \FostyFost\Drawwwr\DrawwwrElement $drawwwrElement
     */
    public function setDrawwwrElement(DrawwwrElement $drawwwrElement)
    {
        $this->drawwwrElement = $drawwwrElement;
    }

    /**
     * @return \FostyFost\Drawwwr\DrawwwrElement
     */
    public function getDrawwwrElement()
    {
        return $this->drawwwrElement;
    }

    /**
     * @param \FostyFost\Drawwwr\Image $image
     */
    public function setImage(Image $image)
    {
        $this->image = $image;
    }

    /**
     * @return \FostyFost\Drawwwr\Image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param \FostyFost\Drawwwr\H5I $h5i
     */
    public function setH5i(H5I $h5i)
    {
        $this->h5i = $h5i;
    }

    /**
     * @return \FostyFost\Drawwwr\H5I
     */
    public function getH5i()
    {
        return $this->h5i;
    }

    /**
     * @param \FostyFost\Drawwwr\Password $password
     */
    public function setPassword(Password $password)
    {
        $this->password = $password;
    }

    /**
     * @return \FostyFost\Drawwwr\Password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return bool
     * @throws \Bitrix\Main\LoaderException
     */
    public static function isInstalled()
    {
        return Main\ModuleManager::isModuleInstalled(DRAWWWR_MODULE_ID);
    }
}
