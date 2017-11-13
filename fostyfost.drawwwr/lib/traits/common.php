<?php

namespace FostyFost\Drawwwr\Traits;

use \Bitrix\Main;
use \Bitrix\Main\Localization\Loc;

/**
 * Trait Common
 *
 * @package FostyFost\Drawwwr\Traits
 * Common main trait for all basis components
 */
trait Common
{
    /**
     * @param array $modules
     *
     * @throws \Bitrix\Main\LoaderException
     */
    protected function checkModules($modules = [])
    {
        if (!is_array($modules) || empty($modules)) {
            return;
        }

        foreach ($modules as $module) {
            if (empty($module)) {
                continue;
            }

            if (!Main\Loader::includeModule($module)) {
                throw new Main\LoaderException("Failed include module \"{$module}\"");
            }
        }
    }

    /**
     * @throws \Exception
     */
    protected function checkPanel()
    {
        $request = Main\Context::getCurrent()->getRequest();

        if (
            \CTopPanel::shouldShowPanel()
            && $request->get('SHOW_PANEL') === 'Y'
            && $request->get('DISABLE_CHECK') !== 'Y'
        ) {
            throw new \Exception(Loc::getMessage('FF_ERROR_SHOW_PANEL'));
        }
    }

    /**
     * @throws \Exception
     */
    protected function checkIncludeAreas()
    {
        global $APPLICATION;

        if (
            $APPLICATION->GetShowIncludeAreas()
            && Main\Context::getCurrent()->getRequest()->get('DISABLE_CHECK') !== 'Y'
        ) {
            throw new \Exception(Loc::getMessage('FF_ERROR_BITRIX_INCLUDE_AREAS'));
        }
    }

    /**
     * Set status 404 and throw exception
     *
     * @param bool $notifier - Sent notify to admin email
     * @param \Exception|null $exception - Exception which will be throwing or 'false' what not throwing exceptions.
     *        Default — throw new \Exception()
     *
     * @throws \Exception
     */
    public function return404($notifier = false, \Exception $exception = null)
    {
        if (!defined('ERROR_404')) {
            define('ERROR_404', 'Y');
        }

        \CHTTP::SetStatus('404 Not Found');

        /** @global \CMain $APPLICATION */
        global $APPLICATION;

        if ($APPLICATION->RestartWorkarea()) {
            if (!defined("BX_URLREWRITE")) {
                define("BX_URLREWRITE", true);
            }

            Main\Composite\Engine::setEnable(false);

            /** @noinspection PhpIncludeInspection */
            require(Main\Application::getDocumentRoot() . '/404.php');

            die();
        }

        if ($exception !== false) {
            if ($notifier === false) {
                $this->exceptionNotifier = false;
            }

            if ($exception instanceof \Exception) {
                throw $exception;
            } else {
                throw new \Exception('Page not found');
            }
        }
    }
}
