<?php

namespace FostyFost\Drawwwr\Debug;

use \Bitrix\Main;

/**
 * Class Debug
 *
 * @package FostyFost\Drawwwr\Debug
 */
class Debug extends Main\Diag\Debug
{
    /** @var string $result */
    private static $result = '';

    /** @var bool $useBacktrace */
    public static $useBacktrace = true;

    /**
     * @param $data
     */
    private static function make($data)
    {
        self::$result = '<kbd id="ff-r"><kbd>';

        Debug::recursive($data);

        if (self::$useBacktrace) {
            $backtrace = Main\Diag\Helper::getBackTrace();

            self::$result .= '</kbd><b class="ff-d"><pre>' . print_r($backtrace, true) . '</pre></b></kbd>';
        }
    }

    /**
     * @param $data
     * @param string $filePath
     */
    public static function saveToFile($data, $filePath = 'drawwwr_debug.log')
    {
        if (empty($data)) {
            return;
        }

        self::make($data);

        file_put_contents(self::makeFilePath($filePath), self::$result);
    }

    /**
     * @param $data
     * @param bool $inPlace
     */
    public static function toScreen($data, $inPlace = true)
    {
        if (empty($data)) {
            return;
        }

        self::make($data);

        \CJSCore::Init(["fostyfost_drawwwr_debug"]);

        if ($inPlace) {
            echo self::$result;

            return;
        }

        global $APPLICATION;

        $APPLICATION->AddViewContent('fostyfost_drawwwr_debug', self::$result, 1);
    }

    /**
     * @param string $filePath
     * @param bool $inPlace
     */
    public static function toScreenFromFile($filePath = 'drawwwr_debug.log', $inPlace = true)
    {
        if (!file_exists(self::makeFilePath($filePath))) {
            return;
        }

        self::toScreen(file_get_contents($filePath), $inPlace);
    }

    /**
     * @param string $data
     * @param string $title
     * @param string $type
     * @param bool $echo
     * @param bool $skip
     *
     * @return string
     */
    public static function toConsole(
        $data = 'EMPTY',
        $title = 'DEBUG',
        $type = 'log',
        $echo = true,
        $skip = true
    ) {
        /** @var int $id */
        static $id = 0;

        $backtrace = Main\Diag\Helper::getBackTrace();

        $result = "<script id=\"CONSOLE_LOG_DEBUG_{$id}\"" . ($skip ? ' data-skip-moving="true"' : '') . '>';

        if (!empty($backtrace)) {
            $result .= "console.{$type}('BACKTRACE'," . json_encode($backtrace) . ');';
        }

        $result .= "console.{$type}(" . json_encode($title) . ',';

        $result .= json_encode($data) . ');</script>';

        if ($echo) {
            echo $result;
        }

        $id++;

        return $result;
    }

    /**
     * @param string $filePath
     * @param string $title
     * @param string $type
     * @param bool $echo
     * @param bool $skip
     *
     * @return string
     */
    public static function toConsoleFromFile(
        $filePath = 'drawwwr_debug.log',
        $title = 'DEBUG',
        $type = 'log',
        $echo = true,
        $skip = true
    ) {
        $filePath = self::makeFilePath($filePath);

        return self::toConsole(
            file_exists($filePath) ? file_get_contents($filePath) : 'EMPTY',
            $title,
            $type,
            $echo,
            $skip
        );
    }

    /**
     * @param $data
     * @param null $name
     */
    private static function recursive($data, $name = null)
    {
        switch (gettype($data)) {
            case 'boolean':
                self::$result .= Debug::getHtml('B', 0, $data ? 'true' : 'false', true, $name);
                break;
            case 'integer':
                self::$result .= Debug::getHtml('I', strlen((string)$data), $data, true, $name);
                break;

            // Because of historical reason double == float
            case 'double':
                self::$result .= Debug::getHtml('F', strlen((string)$data), $data, true, $name);
                break;

            case 'string':
                self::$result .= Debug::getHtml('S', strlen($data), $data, true, $name);
                break;

            case 'NULL':
                self::$result .= Debug::getHtml('N', 0, $data, true, $name);
                break;

            case 'object':
            case 'array':
                $type = gettype($data) === 'array' ? 'A' : 'O';

                $count = gettype($data) === 'array' ? count($data) : count(get_object_vars($data));

                self::$result .= Debug::getHtml($type, $count, '', false, $name) . '<kbd>';

                foreach ($data as $key => $value) {
                    // Recursive call
                    Debug::recursive($value, $key);
                }

                self::$result .= '</kbd></b>';

                break;
        }
    }

    /**
     * @param $type
     * @param $count
     * @param $data
     * @param bool $close
     * @param string $name
     *
     * @return string
     */
    private static function getHtml($type, $count, $data, $close = true, $name = '...')
    {
        $count = (int)$count;

        if ($count <= 0) {
            $count = '';
        }

        $tmp = '';

        if ($type === 'A' || $type === 'O') {
            $tmp .= "<b class=\"ff-i\"><div><h2>{$name}</h2><i>{$type}</i><s>{$count}</s></div>";

            /** @noinspection PhpUnusedLocalVariableInspection */
            $name = $name == '' ? '0' : $name;
        } else {
            $tmp .= "<b><h2>{$name}</h2><i>{$type}</i><s>{$count}</s><p>{$data}</p>";

            /** @noinspection PhpUnusedLocalVariableInspection */
            $name = $name == '' ? '...' : $name;
        }

        $tmp .= $close ? '</b>' : '';

        return $tmp;
    }

    /**
     * @param $filePath
     *
     * @return string
     */
    private static function makeFilePath($filePath)
    {
        return Main\Application::getDocumentRoot()
               . '/' . (!empty($filePath) ? $filePath : 'drawwwr_debug.log');
    }
}
