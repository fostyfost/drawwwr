<?php

namespace FostyFost\Drawwwr\Components;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

if (!\Bitrix\Main\Loader::includeModule(DRAWWWR_MODULE_ID)) {
    return;
}

/**
 * Необходимо для корректного поиска класса PageNavigationComponent
 *
 * @see \PageNavigationComponent
 */
\CBitrixComponent::includeComponentClass('bitrix:main.pagenavigation');

/**
 * Class DrawwwrPageNavigationComponent
 */
class DrawwwrPageNavigationComponent extends \PageNavigationComponent
{
    /**
     * DrawwwrPageNavigationComponent constructor
     *
     * @param null|\CBitrixComponent $component
     */
    public function __construct($component = null)
    {
        parent::__construct($component);
    }
}
