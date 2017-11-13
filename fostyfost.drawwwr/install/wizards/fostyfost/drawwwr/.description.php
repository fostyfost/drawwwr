<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

$wizardSiteId = \Bitrix\Main\Context::getCurrent()->getRequest()->get('wizardSiteID');

if (!defined('WIZARD_DEFAULT_SITE_ID') && !empty($wizardSiteId)) {
    define('WIZARD_DEFAULT_SITE_ID', $wizardSiteId);
}

$arWizardDescription = [
    "NAME"        => \Bitrix\Main\Localization\Loc::getMessage('DRAWWWR_WIZARD_NAME'),
    "DESCRIPTION" => \Bitrix\Main\Localization\Loc::getMessage('DRAWWWR_WIZARD_DESC'),
    "VERSION"     => '0.0.1',
    "START_TYPE"  => 'WINDOW',
    "WIZARD_TYPE" => "INSTALL",
    "IMAGE"       => 'images/' . LANGUAGE_ID . '/solution.png',
    "PARENT"      => 'wizard_sol',
    "TEMPLATES"   => [["SCRIPT" => 'wizard_sol']],
    "STEPS"       => [
        "SelectTemplateStep",
        "SiteSettingsStep",
        "InstallStep",
        "SuccessStep"
    ]
];
