<?php

namespace FostyFost\Drawwwr\Interfaces;

/**
 * Interface Checkable
 *
 * @package FostyFost\Drawwwr\Interfaces
 */
interface Checkable
{
    /**
     * @return bool
     */
    public function isEmpty();

    /**
     * @return bool
     */
    public function checkLength();
}
