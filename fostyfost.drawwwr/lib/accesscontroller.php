<?php

namespace FostyFost\Drawwwr;

use \Bitrix\Main;

/**
 * Class AccessController
 *
 * @package FostyFost\Drawwwr
 */
class AccessController
{
    /**
     * @param string $formType
     * @param string $message
     * @param string $signedParams
     */
    public static function showMessage($formType = 'ERROR', $message = '', $signedParams = '')
    {
        $documentRoot = Main\Application::getDocumentRoot();

        \CMain::PrologActions();

        /** @noinspection PhpIncludeInspection */
        include($documentRoot . BX_ROOT . '/modules/main/include/prolog_after.php');

        global $APPLICATION;

        $APPLICATION->IncludeComponent(
            'fostyfost:drawwwr.auth',
            '.default',
            [
                "FORM_TYPE"   => $formType,
                "MESSAGE"     => $message,
                "AJAX_PARAMS" => $signedParams
            ]
        );

        /** @noinspection PhpIncludeInspection */
        include($documentRoot . BX_ROOT . '/modules/main/include/epilog.php');

        die();
    }

    /**
     * @param $id
     *
     * @return bool
     * @throws \Bitrix\Main\ArgumentException
     */
    public static function isEditable($id)
    {
        $drawwwrElement = Model\DrawwwrFilesTable::getList([
            "filter" => ["ID" => $id],
            "select" => ["CAN_EDIT"],
            "limit"  => 1
        ])->fetch();

        return $drawwwrElement["CAN_EDIT"] === 'Y';
    }

    /**
     * @param $elementCode
     *
     * @return bool
     */
    public static function isAuthorFor($elementCode)
    {
        return in_array($elementCode, $_SESSION["DRAWWWR"]["AUTHOR_FOR"]);
    }

    /**
     * @param $id
     *
     * @return bool
     */
    public static function logIn($id)
    {
        $result = false;

        try {
            Main\Loader::includeModule('iblock');

            $element = Model\DrawwwrFilesTable::getList([
                "filter" => ["=ID" => $id],
                "select" => [
                    "ID",
                    "PASSWORD",
                    "ELEMENT_CODE" => "ELEMENT.CODE"
                ],
                "limit"  => 1
            ])->fetch();

            $drawwwrInstance = Drawwwr::getInstance();

            $drawwwrInstance->setPassword(new Password($element["PASSWORD"]));

            if ($drawwwrInstance->getPassword()->checkHash($_POST["DRAWWWR_PASSWORD"])) {
                $_SESSION["DRAWWWR"]["AUTHOR_FOR"][] = $element["ELEMENT_CODE"];

                $_SESSION["DRAWWWR"]["CURRENT_ID"] = $element["ID"];

                $result = true;
            }

            unset($drawwwrInstance, $hash);
        } catch (\Exception $e) {
            self::showMessage();
        }

        return $result;
    }

    public static function logOut()
    {
        AccessController::switchEditability($_SESSION["DRAWWWR"]["CURRENT_ID"], 'Y');

        \BXClearCache(true, '/drawwwr/');

        $_SESSION["DRAWWWR"] = [];
    }

    /**
     * @param $id
     * @param string $value
     *
     * @return bool
     */
    public static function switchEditability($id, $value = 'N')
    {
        try {
            $switchResult = Model\DrawwwrFilesTable::update($id, ["CAN_EDIT" => $value]);

            $result = $switchResult->isSuccess();
        } catch (\Exception $e) {
            $result = false;
        }

        return $result;
    }
}