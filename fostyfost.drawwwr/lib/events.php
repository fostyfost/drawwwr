<?php

namespace FostyFost\Drawwwr;

use \Bitrix\Main;
use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class Events
 *
 * @package FostyFost\Drawwwr
 */
class Events
{
    /**
     * @param \Bitrix\Main\Event $event
     *        Expected parameters:
     *          ID,
     *          ELEMENT_CODE,
     *          CAN_EDIT,
     *          H5I_FILE_ID,
     *          IMAGE_FILE_ID
     *
     * @return \Bitrix\Main\EventResult
     * @throws \Bitrix\Main\ArgumentTypeException
     * @throws \Exception
     */
    public static function onBeforeEdit(Main\Event $event)
    {
        $element = $event->getParameters();

        if (empty($element["ID"]) || empty($element["ELEMENT_CODE"])) {
            AccessController::showMessage();

            return new Main\EventResult(
                Main\EventResult::ERROR,
                $element,
                DRAWWWR_MODULE_ID
            );
        }

        $drawwwrInstance = Drawwwr::getInstance();

        $drawwwrInstance->setDrawwwrElement(new DrawwwrElement());

        $drawwwrElement = $drawwwrInstance->getDrawwwrElement();

        $drawwwrElement->setId($element["ID"]);

        if (AccessController::isAuthorFor($element["ELEMENT_CODE"])) {
            if (
                $element["CAN_EDIT"] === 'Y'
                && !AccessController::switchEditability($element["ID"])
            ) {
                AccessController::showMessage();

                return new Main\EventResult(
                    Main\EventResult::ERROR,
                    $element,
                    DRAWWWR_MODULE_ID
                );
            }

            \BXClearCache(true, '/drawwwr/');

            return new Main\EventResult(
                Main\EventResult::SUCCESS,
                $element,
                DRAWWWR_MODULE_ID
            );
        } elseif (
            $element["CAN_EDIT"] === 'Y'
            && !AccessController::isAuthorFor($element["ELEMENT_CODE"])
        ) {
            $signedParams = (new Main\Security\Sign\Signer)->sign(
                base64_encode(serialize($element)),
                'drawwwr.auth'
            );

            AccessController::showMessage(
                'PASSWORD',
                Loc::getMessage('FF_NEED_PASSWORD'),
                $signedParams
            );

            return new Main\EventResult(
                Main\EventResult::ERROR,
                $element,
                DRAWWWR_MODULE_ID
            );
        } elseif (
            $element["CAN_EDIT"] === 'N'
            && !AccessController::isAuthorFor($element["ELEMENT_CODE"])
        ) {
            AccessController::showMessage();

            return new Main\EventResult(
                Main\EventResult::ERROR,
                $element,
                DRAWWWR_MODULE_ID
            );
        }

        AccessController::showMessage();

        return new Main\EventResult(
            Main\EventResult::UNDEFINED,
            $element,
            DRAWWWR_MODULE_ID
        );
    }
}
