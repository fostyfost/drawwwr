<?php

namespace FostyFost\Drawwwr;

use \Bitrix\Main;
use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class BaseComponent
 *
 * @package FostyFost\Drawwwr
 */
abstract class BaseComponent extends \CBitrixComponent
{
    /** @var bool $exceptionNotifier - Sending notifications to admin email */
    protected $exceptionNotifier = true;

    /**
     * Called when an error occurs
     * Resets the cache, show error message (two mode: for users and for admins),
     * sending notification to admin email
     *
     * @param \Exception $exception
     * @param null|bool $notifier - Sent notify to admin email.
     *        Default — value of property $this->exceptionNotifier
     *
     * @uses exceptionNotifier
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     */
    private function catchException(\Exception $exception, $notifier = null)
    {
        global $USER;

        $this->abortResultCache();

        if ($USER->IsAdmin()) {
            $this->showExceptionAdmin($exception);
        } else {
            $this->showExceptionUser();
        }

        if (($notifier === true) || ($this->exceptionNotifier && $notifier !== false)) {
            $this->sendNotifyException($exception);
        }
    }

    /**
     * Send error message to the admin email
     *
     * @param \Exception $exception
     *
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     */
    private function sendNotifyException($exception)
    {
        $adminEmail = Main\Config\Option::get('main', 'email_from');

        if (empty($adminEmail)) {
            return;
        }

        $request = Main\Context::getCurrent()->getRequest();

        $protocol = $request->isHttps() ? 'https://' : 'http://';

        \bxmail(
            $adminEmail,
            Loc::getMessage(
                'FF_COMPONENT_EXCEPTION_EMAIL_SUBJECT',
                ["#SITE_URL#" => SITE_SERVER_NAME]
            ),
            Loc::getMessage(
                'FF_COMPONENT_EXCEPTION_EMAIL_TEXT',
                [
                    "#URL#"               => $protocol . SITE_SERVER_NAME . $request->getRequestedPage(),
                    "#DATE#"              => date('Y-m-d H:m:s'),
                    "#EXCEPTION_MESSAGE#" => $exception->getMessage(),
                    "#EXCEPTION#"         => $exception
                ]
            ),
            'Content-Type: text/html; charset=utf-8'
        );
    }

    /**
     * Display of the error for user
     */
    private function showExceptionUser()
    {
        \ShowError(Loc::getMessage('FF_COMPONENT_CATCH_EXCEPTION'));
    }

    /**
     * Display of the error for admin
     *
     * @param \Exception $exception
     */
    private function showExceptionAdmin(\Exception $exception)
    {
        \ShowError($exception->getMessage());

        echo nl2br($exception);
    }

    abstract protected function prepareData();

    abstract protected function prepareComponent();

    /**
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     */
    public function executeComponent()
    {
        try {
            $this->prepareComponent();
        } catch (\Exception $e) {
            $this->catchException($e);
        }
    }
}
