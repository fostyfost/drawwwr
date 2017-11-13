<?php

define('DRAWWWR_TYPE', 'drawwwr');

define('DRAWWWR_MODULE_ID', 'fostyfost.drawwwr');

\Bitrix\Main\Loader::registerAutoLoadClasses(
    DRAWWWR_MODULE_ID,
    [
        "\\FostyFost\\Drawwwr\\Drawwwr"                           => 'lib/drawwwr.php',
        "\\FostyFost\\Drawwwr\\Events"                            => 'lib/events.php',
        "\\FostyFost\\Drawwwr\\IblockElement"                     => 'lib/iblockelement.php',
        "\\FostyFost\\Drawwwr\\DrawwwrElement"                    => 'lib/drawwwrelement.php',
        "\\FostyFost\\Drawwwr\\Image"                             => 'lib/image.php',
        "\\FostyFost\\Drawwwr\\H5I"                               => 'lib/h5i.php',
        "\\FostyFost\\Drawwwr\\Password"                          => 'lib/password.php',
        "\\FostyFost\\Drawwwr\\AccessController"                  => 'lib/accesscontroller.php',
        "\\FostyFost\\Drawwwr\\BaseComponent"                     => 'lib/basecomponent.php',
        "\\FostyFost\\Drawwwr\\Traits\\Common"                    => 'lib/traits/common.php',
        "\\FostyFost\\Drawwwr\\Interfaces\\Checkable"             => 'lib/interfaces/checkable.php',
        "\\FostyFost\\Drawwwr\\Interfaces\\Element"               => 'lib/interfaces/element.php',
        "\\FostyFost\\Drawwwr\\Helpers\\ComponentParameters"      => 'lib/helpers/componentparameters.php',
        "\\FostyFost\\Drawwwr\\Model\\DrawwwrFilesTable"          => 'lib/model/drawwwrfilestable.php',
        "\\FostyFost\\Drawwwr\\Model\\IBlockElementPropertyTable" => 'lib/model/iblockelementpropertytable.php',
        "\\FostyFost\\Drawwwr\\Debug\\Events"                     => 'lib/debug/events.php',
        "\\FostyFost\\Drawwwr\\Debug\\Debug"                      => 'lib/debug/debug.php'
    ]
);

if (!function_exists('_stf')) {
    /**
     * @see \FostyFost\Drawwwr\Debug\Debug::saveToFile
     *
     * @param $data
     * @param string $filePath
     *
     * @return bool|mixed
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     */
    function _stf($data, $filePath = 'drawwwr_debug.log')
    {
        if (!\CSite::InGroup(
            explode(',', \Bitrix\Main\Config\Option::get(DRAWWWR_MODULE_ID, 'FF_DEBUG_GROUPS', ''))
        )) {
            return false;
        }

        return call_user_func_array(
            [
                "\\FostyFost\\Drawwwr\\Debug\\Debug",
                "saveToFile"
            ],
            [
                $data,
                $filePath
            ]
        );
    }
}

if (!function_exists('_ts')) {
    /**
     * @see \FostyFost\Drawwwr\Debug\Debug::toScreen
     *
     * @param $data
     * @param bool $inPlace
     *
     * @return bool|mixed
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     */
    function _ts($data, $inPlace = true)
    {
        if (!\CSite::InGroup(
            explode(',', \Bitrix\Main\Config\Option::get(DRAWWWR_MODULE_ID, 'FF_DEBUG_GROUPS', ''))
        )) {
            return false;
        }

        return call_user_func_array(
            [
                "\\FostyFost\\Drawwwr\\Debug\\Debug",
                "toScreen"
            ],
            [
                $data,
                $inPlace
            ]
        );
    }
}

if (!function_exists('_tsff')) {
    /**
     * @see \FostyFost\Drawwwr\Debug\Debug::toScreenFromFile
     *
     * @param string $filePath
     * @param bool $inPlace
     *
     * @return bool|mixed
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     */
    function _tsff($filePath = 'drawwwr_debug.log', $inPlace = true)
    {
        if (!\CSite::InGroup(
            explode(',', \Bitrix\Main\Config\Option::get(DRAWWWR_MODULE_ID, 'FF_DEBUG_GROUPS', ''))
        )) {
            return false;
        }

        return call_user_func_array(
            [
                "\\FostyFost\\Drawwwr\\Debug\\Debug",
                "toScreenFromFile"
            ],
            [
                $filePath,
                $inPlace
            ]
        );
    }
}

if (!function_exists('_cons')) {
    /**
     * @see \FostyFost\Drawwwr\Debug\Debug::toConsole
     *
     * @param string $data
     * @param string $title
     * @param string $type
     * @param bool $echo
     * @param bool $skip
     *
     * @return bool|mixed
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     */
    function _cons($data = 'EMPTY', $title = 'DEBUG', $type = 'log', $echo = true, $skip = true)
    {
        if (!\CSite::InGroup(
            explode(',', \Bitrix\Main\Config\Option::get(DRAWWWR_MODULE_ID, 'FF_DEBUG_GROUPS', ''))
        )) {
            return false;
        }

        return call_user_func_array(
            [
                "\\FostyFost\\Drawwwr\\Debug\\Debug",
                "toConsole"
            ],
            [
                $data,
                $title,
                $type,
                $echo,
                $skip
            ]
        );
    }
}

if (!function_exists('_tcff')) {
    /**
     * @see \FostyFost\Drawwwr\Debug\Debug::toConsoleFromFile
     *
     * @param string $filePath
     * @param string $title
     * @param string $type
     * @param bool $echo
     * @param bool $skip
     *
     * @return bool|mixed
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     */
    function _tcff($filePath = 'drawwwr_debug.log', $title = 'DEBUG', $type = 'log', $echo = true, $skip = true)
    {
        if (!\CSite::InGroup(
            explode(',', \Bitrix\Main\Config\Option::get(DRAWWWR_MODULE_ID, 'FF_DEBUG_GROUPS', ''))
        )) {
            return false;
        }

        return call_user_func_array(
            [
                "\\FostyFost\\Drawwwr\\Debug\\Debug",
                "toConsoleFromFile"
            ],
            [
                $filePath,
                $title,
                $type,
                $echo,
                $skip
            ]
        );
    }
}
