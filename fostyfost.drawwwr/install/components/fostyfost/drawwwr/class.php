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
 * Class DrawwwrComponent
 *
 * @package FostyFost\Drawwwr\Components
 * @property array $arResult
 * @see \FostyFost\Drawwwr\BasisRouter
 */
class DrawwwrComponent extends Drawwwr\BaseComponent
{
    use Drawwwr\Traits\Common;

    /** @var array $defaultUrlTemplates404 */
    protected $defaultUrlTemplates404 = [
        "list"   => '',
        "edit"   => 'edit/#ELEMENT_CODE#/',
        "create" => 'create/'
    ];

    /** @var array $componentVariables */
    protected $componentVariables = [
        "ELEMENT_CODE",
        "create"
    ];

    /** @var string $sefFolder */
    protected $sefFolder;

    protected $defaultPage = 'list';

    protected $defaultSefPage = 'list';

    /** @var array $urlTemplates */
    protected $urlTemplates;

    /** @var array $variables */
    protected $variables;

    /** @var array $variableAliases */
    protected $variableAliases;

    /** @var string $templatePage */
    protected $templatePage;

    /**
     * @throws \Bitrix\Main\LoaderException
     * @throws \Exception
     */
    protected function setPage()
    {
        $variables = [];

        $requestedPage = Main\Context::getCurrent()->getRequest()->getRequestedPage();

        if ($this->arParams["SEF_MODE"] === 'Y') {
            $urlTemplates = \CComponentEngine::makeComponentUrlTemplates(
                $this->defaultUrlTemplates404,
                []
            );

            $variableAliases = \CComponentEngine::makeComponentVariableAliases(
                $this->defaultUrlTemplates404,
                []
            );

            $this->templatePage = \CComponentEngine::parseComponentPath(
                $this->arParams["SEF_FOLDER"],
                $urlTemplates,
                $variables
            );

            $b404 = false;

            if (!$this->templatePage) {
                $this->templatePage = $this->defaultSefPage;

                $b404 = true;
            }

            if ($b404 && Main\Loader::includeModule('iblock')) {
                $folder404 = str_replace('\\', '/', $this->arParams["SEF_FOLDER"]);

                if ($folder404 !== '/') {
                    $folder404 = '/' . trim($folder404, "/ \t\n\r\0\x0B") . '/';
                }

                if (substr($folder404, -1) === '/') {
                    $folder404 .= 'index.php';
                }

                if ($folder404 !== $requestedPage) {
                    $this->return404();
                }
            }

            $this->sefFolder = $this->arParams["SEF_FOLDER"];

            \CComponentEngine::initComponentVariables(
                $this->templatePage,
                $this->componentVariables,
                $this->variableAliases,
                $variables
            );
        } else {
            $variableAliases = \CComponentEngine::makeComponentVariableAliases(
                $this->defaultUrlTemplates404,
                []
            );

            \CComponentEngine::initComponentVariables(
                false,
                $this->componentVariables,
                $variableAliases,
                $variables
            );

            if (isset($variables["ELEMENT_CODE"]) && strlen($variables["ELEMENT_CODE"]) > 0) {
                $this->templatePage = 'edit';
            } elseif (isset($variables["create"]) && $variables["create"] === 'y') {
                $this->templatePage = 'create';
            } else {
                $this->templatePage = $this->defaultPage;
            }

            $urlTemplates = [
                "list"   => $requestedPage,
                "create" => "{$requestedPage}?create=y",
                "edit"   => "{$requestedPage}?ELEMENT_CODE=#ELEMENT_CODE#"
            ];
        }

        $this->urlTemplates = $urlTemplates;

        $this->variables = $variables;

        $this->variableAliases = $variableAliases;
    }

    protected function prepareData()
    {
        $this->arResult["FOLDER"] = $this->sefFolder;

        $this->arResult["URL_TEMPLATES"] = $this->urlTemplates;

        $this->arResult["VARIABLES"] = $this->variables;

        $this->arResult["ALIASES"] = $this->variableAliases;
    }

    /**
     * @throws \Bitrix\Main\LoaderException
     */
    protected function prepareComponent()
    {
        $this->setPage();

        $this->prepareData();

        $this->includeComponentTemplate($this->templatePage);
    }
}
