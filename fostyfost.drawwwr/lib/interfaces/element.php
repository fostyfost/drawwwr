<?php

namespace FostyFost\Drawwwr\Interfaces;

/**
 * Interface Element
 *
 * @package FostyFost\Drawwwr\Interfaces
 */
interface Element
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @param $id
     */
    public function setId($id);

    /**
     * @return bool
     */
    public function create();

    /**
     * @return bool
     */
    public function update();

    /**
     * @return void
     */
    public function remove();
}
