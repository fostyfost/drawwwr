<?php

namespace FostyFost\Drawwwr\Debug;

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Context;

Loc::loadMessages(__FILE__);

/**
 * Class Events
 *
 * @package FostyFost\Drawwwr\Debug
 */
class Events
{
    /**
     * @param array $aGlobalMenu
     * @param array $aModuleMenu
     */
    public static function buildDebugMenu(
        /** @noinspection PhpUnusedParameterInspection */
        &$aGlobalMenu,
        &$aModuleMenu
    ) {
        $aModuleMenu[] = [
            "parent_menu" => 'global_menu_settings',
            "section"     => DRAWWWR_MODULE_ID,
            "sort"        => 50,
            "text"        => Loc::getMessage('FF_DEBUG_TEXT'),
            "title"       => Loc::getMessage('FF_DEBUG_TITLE'),
            "url"         => 'fostyfost.drawwwr_debug_admin.php',
            "icon"        => 'ff_debug_menu_icon',
            "page_icon"   => 'ff_debug_page_icon',
            "items_id"    => 'fostyfost.drawwwr_debug_items',
            "more_url"    => [],
            "items"       => []
        ];
    }

    public static function showDebugContent()
    {
        global $APPLICATION;

        $APPLICATION->ShowViewContent('fostyfost_drawwwr_debug');
    }

    public static function initDebug()
    {
        $tmpOptionsArray = [
            "js"  => '/bitrix/js/fostyfost.drawwwr/script.js',
            "css" => '/bitrix/themes/.default/fostyfost.drawwwr_debug.css'
        ];

        if (Option::get(DRAWWWR_MODULE_ID, 'FF_DEBUG_JQUERY', 'N') === 'Y') {
            $tmpOptionsArray["rel"] = ["jquery"];
        }

        \CJSCore::RegisterExt('fostyfost_drawwwr_debug', $tmpOptionsArray);

        \CJSCore::RegisterExt(
            'fostyfost_drawwwr_debug_css',
            ["css" => '/bitrix/themes/.default/fostyfost.drawwwr_debug.css']
        );

        if (defined('ADMIN_SECTION') && ADMIN_SECTION === true) {
            \CJSCore::Init(["fostyfost_drawwwr_debug_css"]);
        }

        global $APPLICATION;

        $postList = Context::getCurrent()->getRequest()->getPostList();

        if (
            $APPLICATION->GetCurPage() === '/bitrix/admin/fostyfost.drawwwr_debug_admin.php'
            && (isset($postList["FF_DEBUG_JQUERY"]) || isset($postList["FF_DEBUG_GROUPS"]))
        ) {
            Option::set(DRAWWWR_MODULE_ID, 'FF_DEBUG_JQUERY', $postList["FF_DEBUG_JQUERY"]);
            Option::set(DRAWWWR_MODULE_ID, 'FF_DEBUG_GROUPS', implode(',', $postList["FF_DEBUG_GROUPS"]));
        }
    }
}
