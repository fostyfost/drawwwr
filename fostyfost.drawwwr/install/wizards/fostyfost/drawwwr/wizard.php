<?php

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Application;
use \Bitrix\Main\SiteTable;
use \Bitrix\Main\SiteTemplateTable;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

Loc::loadMessages(__FILE__);

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */

/**
 * Class SelectTemplateStep
 */
class SelectTemplateStep extends \CWizardStep
{
    /** @var int $sitesCounter */
    private $sitesCounter = 0;

    public function InitStep()
    {
        $this->SetStepID('set_template');

        $this->SetTitle(Loc::getMessage('WIZ_TEMPLATE_SETTINGS'));

        $this->sitesCounter = SiteTable::getCount();

        if ($this->sitesCounter < 2) {
            $this->SetNextStep('set_site');

            $this->SetNextCaption(Loc::getMessage('WIZ_MAIN_SETTINGS'));

            $this->GetWizard()->SetDefaultVars([
                "templateDescription" => Loc::getMessage('FF_DRAWWWR_WIZ_TEMPLATE_DESCRIPTION_DEFAULT'),
                "templateName"        => Loc::getMessage('FF_DRAWWWR_WIZ_TEMPLATE_NAME_DEFAULT'),
                "templateDir"         => Loc::getMessage('FF_DRAWWWR_WIZ_TEMPLATE_DIR_DEFAULT')
            ]);
        } else {
            $documentRoot = Application::getDocumentRoot();

            $siteWizard = file_get_contents(
                "{$documentRoot}/bitrix/wizards/fostyfost/drawwwr/site/_index.php"
            );

            $siteWizard = strtr(
                $siteWizard,
                [
                    "#SITE_ID#"       => SITE_ID,
                    "#SITE_ENCODING#" => SITE_CHARSET
                ]
            );

            file_put_contents($documentRoot . SITE_DIR . 'index.php', $siteWizard);

            $this->SetNextStep('success');

            $this->SetNextCaption(Loc::getMessage('WIZ_FINISH'));
        }
    }

    public function OnPostForm()
    {
        if ($this->sitesCounter < 2) {
            $templateDir = $this->GetWizard()->GetVar('templateDir');

            if (!preg_match('#^[A-Za-z0-9_]+$#is', $templateDir)) {
                $this->SetError(Loc::getMessage('WIZ_TEMPLATE_DIR_ERROR'));
            }
        }
    }

    public function ShowStep()
    {
        if ($this->sitesCounter < 2) {
            $this->content .= '<div class="wizard-input-form">'
                              . '<div class="wizard-input-form-block">'
                              . '<h4><label for="siteName">'
                              . Loc::getMessage('WIZ_TEMPLATE_NAME')
                              . '</label></h4>'
                              . '<div class="wizard-input-form-block-content">'
                              . '<div class="wizard-input-form-field wizard-input-form-field-text">'
                              . $this->ShowInputField('text', 'templateName')
                              . '</div></div></div>'
                              . '<div class="wizard-input-form-block">'
                              . '<h4><label for="siteName">'
                              . Loc::getMessage('WIZ_TEMPLATE_DESCRIPTION')
                              . '</label></h4>'
                              . '<div class="wizard-input-form-block-content">'
                              . '<div class="wizard-input-form-field wizard-input-form-field-text">'
                              . $this->ShowInputField('text', 'templateDescription')
                              . '</div></div></div>'
                              . '<div class="wizard-input-form-block">'
                              . '<h4><label for="siteName">'
                              . Loc::getMessage('WIZ_TEMPLATE_DIR')
                              . '</label></h4>'
                              . '<div class="wizard-input-form-block-content">'
                              . '<div class="wizard-input-form-field wizard-input-form-field-text">'
                              . $this->ShowInputField('text', 'templateDir')
                              . '</div></div></div></div>';
        } else {
            $this->content .= Loc::getMessage('WIZ_NOT_FIRST');
        }
    }
}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */

/**
 * Class SiteSettingsStep
 */
class SiteSettingsStep extends \CWizardStep
{
    public function InitStep()
    {
        $this->SetStepID('set_site');

        $this->SetTitle(Loc::getMessage('WIZ_MAIN_SETTINGS'));

        $this->SetNextStep('install_step');

        $this->SetCancelCaption(Loc::getMessage('WIZ_MAIN_SETTINGS'));

        $this->GetWizard()->SetDefaultVars([
            "siteName"            => Loc::getMessage('FF_DRAWWWR_WIZ_SETTINGS_SITE_NAME_DEFAULT'),
            "siteMetaTitle"       => Loc::getMessage('FF_DRAWWWR_WIZ_SETTINGS_TITLE_DEFAULT'),
            "siteMetaDescription" => Loc::getMessage('FF_DRAWWWR_WIZ_SETTINGS_DESCRIPTION_DEFAULT'),
            "siteMetaKeywords"    => Loc::getMessage('FF_DRAWWWR_WIZ_SETTINGS_KEYWORDS_DEFAULT')
        ]);
    }

    public function OnPostForm()
    {
    }

    public function ShowStep()
    {
        $this->content .= '<div class="wizard-input-form">'
                          . '<div class="wizard-input-form-block"><h4><label for="siteName">'
                          . Loc::getMessage('WIZ_SETTINGS_SITE_NAME')
                          . '</label></h4><div class="wizard-input-form-block-content">'
                          . '<div class="wizard-input-form-field wizard-input-form-field-text">'
                          . $this->ShowInputField('text', 'siteName')
                          . '</div></div></div><div class="wizard-input-form-block"><h4><label for="siteName">'
                          . Loc::getMessage('WIZ_SETTINGS_TITLE')
                          . '</label></h4><div class="wizard-input-form-block-content">'
                          . '<div class="wizard-input-form-field wizard-input-form-field-text">'
                          . $this->ShowInputField('text', 'siteMetaTitle')
                          . '</div></div></div><div class="wizard-input-form-block"><h4><label for="siteName">'
                          . Loc::getMessage('WIZ_SETTINGS_DESCRIPTION')
                          . '</label></h4><div class="wizard-input-form-block-content">'
                          . '<div class="wizard-input-form-field wizard-input-form-field-text">'
                          . $this->ShowInputField('text', 'siteMetaDescription')
                          . '</div></div></div><div class="wizard-input-form-block">'
                          . '<h4><label for="siteName">'
                          . Loc::getMessage('WIZ_SETTINGS_KEYWORDS')
                          . '</label></h4><div class="wizard-input-form-block-content">'
                          . '<div class="wizard-input-form-field wizard-input-form-field-text">'
                          . $this->ShowInputField('text', 'siteMetaKeywords')
                          . '</div></div></div></div>';
    }
}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */

/**
 * Class InstallStep
 */
class InstallStep extends \CWizardStep
{
    public function InitStep()
    {
        $this->SetStepID('install_step');

        $this->SetTitle(Loc::getMessage('WIZ_INSTALL'));
    }

    public function OnPostForm()
    {
        $currentStep = $this->GetWizard()->GetVar('nextStep');

        if ($currentStep < 1) {
            $currentStep = 0;
        }

        $installSteps = [
            Loc::getMessage('WIZ_TEMPLATE_INSTALL'),
            Loc::getMessage('WIZ_SETTINGS_INSTALL')
        ];

        if (!isset($installSteps[$currentStep])) {
            $this->GetWizard()->SetCurrentStep('success');

            return;
        }

        $documentRoot = Application::getDocumentRoot();

        $arVars = $this->GetWizard()->GetVars();

        switch ($currentStep) {
            case 0:
                \CopyDirFiles(
                    "{$documentRoot}/bitrix/wizards/fostyfost/drawwwr/site/template/",
                    "{$documentRoot}/local/templates/{$arVars["templateDir"]}/",
                    true,
                    true,
                    false
                );

                $templateDescription = file_get_contents(
                    "{$documentRoot}/local/templates/{$arVars["templateDir"]}/description.php"
                );

                $templateDescription = strtr(
                    $templateDescription,
                    [
                        "#TEMPLATE_NAME#"        => $arVars["templateName"],
                        "#TEMPLATE_DESCRIPTION#" => $arVars["templateDescription"]
                    ]
                );

                file_put_contents(
                    "{$documentRoot}/local/templates/{$arVars["templateDir"]}/description.php",
                    $templateDescription
                );

                break;

            case 1:
                \CopyDirFiles(
                    "{$documentRoot}/bitrix/wizards/fostyfost/drawwwr/site/public/" . LANGUAGE_ID . '/',
                    "{$documentRoot}/",
                    true,
                    true,
                    false
                );

                $siteMeta = file_get_contents("{$documentRoot}/.section.php");

                $siteMeta = strtr(
                    $siteMeta,
                    [
                        "#SITE_TITLE#"       => $arVars["siteMetaTitle"],
                        "#SITE_DESCRIPTION#" => $arVars["siteMetaDescription"],
                        "#SITE_KEYWORDS#"    => $arVars["siteMetaKeywords"]
                    ]
                );

                file_put_contents("{$documentRoot}/.section.php", $siteMeta);

                $defaultSite = SiteTable::getList([
                    "filter" => ["=DEF" => 'Y'],
                    "select" => ["LID"]
                ])->fetch();

                if (empty($defaultSite["LID"])) {
                    break;
                }

                /** @var \Bitrix\Main\Entity\UpdateResult $siteUpdateResult */
                $siteUpdateResult = SiteTable::update(
                    $defaultSite["LID"],
                    [
                        "NAME"      => $arVars["siteName"],
                        "SITE_NAME" => $arVars["siteName"],
                        "SORT"      => 1
                    ]
                );

                if (!$siteUpdateResult->isSuccess()) {
                    break;
                }

                $siteTemplates = SiteTemplateTable::getList([
                    "filter" => ["=SITE_ID" => $defaultSite["LID"]],
                    "select" => ["ID"]
                ])->fetchAll();

                if (is_array($siteTemplates) && !empty($siteTemplates)) {
                    foreach ($siteTemplates as $template) {
                        SiteTemplateTable::update(
                            $template["ID"],
                            ["TEMPLATE" => $arVars["templateDir"]]
                        );
                    }

                    break;
                }

                SiteTemplateTable::add([
                    "SITE_ID"   => $defaultSite["LID"],
                    "CONDITION" => '',
                    "SORT"      => 1,
                    "TEMPLATE"  => $arVars["templateDir"]
                ]);

                break;
        }

        if (!isset($installSteps[$currentStep + 1])) {
            $response = "window.ajaxForm.StopAjax(); window.ajaxForm.SetStatus('100'); window.ajaxForm.Post('"
                        . ($currentStep + 1) . "', 'skip', '"
                        . ($currentStep < 2 ? $installSteps[$currentStep] : $installSteps[$currentStep]["status"])
                        . "');";
        } else {
            $progress = round($currentStep / sizeof($installSteps) * 100);

            $response = "window.ajaxForm.SetStatus('{$progress}'); window.ajaxForm.Post('"
                        . ($currentStep + 1) . "', 'skip', '"
                        . ($currentStep < 2 ? $installSteps[$currentStep] : $installSteps[$currentStep]["status"])
                        . "');";
        }

        die("[response]{$response}[/response]");
    }

    public function ShowStep()
    {
        $loaderImagePath = '/bitrix/images/main/wizard_sol/wait.gif';

        $this->content .= '<table border="0" cellspacing="0" cellpadding="2" width="100%">'
                          . '<tr><td colspan="2"><div id="status"></div></td></tr>'
                          . '<tr><td width="90%" height="10">'
                          . '<div style="border: 1px solid #b9cbdf; width: 100%;">'
                          . '<div id="indicator" style="height: 10px; width: 0; background-color: #b9cbdf;"></div>'
                          . '</div></td><td width="10%">&nbsp;<span id="percent">0%</span>'
                          . '<span id="percent2" style="display: none;">0%</span></td></tr></table>'
                          . '<div id="wait" align="center"><br>'
                          . '<table width=200 cellspacing=0 cellpadding=0 border=0 '
                          . 'style="border: 1px solid #efcb69;" bgcolor="#FFF7D7">'
                          . '<tr><td height=50 width="50" valign="middle" align=center><img src="'
                          . $loaderImagePath
                          . '"></td><td height="50" width="150">'
                          . Loc::getMessage('FF_WIZARD_WAIT_WINDOW_TEXT')
                          . '</td></tr></table></div><br><br>'
                          . '<div id="error_container" style="display: none;">'
                          . '<div id="error_notice"><span style="color: red;">'
                          . Loc::getMessage('FF_INST_ERROR_OCCURRED') . '<br>'
                          . Loc::getMessage('FF_INST_TEXT_ERROR') . ':</span></div>'
                          . '<div id="error_text"></div>'
                          . '<div><span style="color: red;">'
                          . Loc::getMessage('FF_INST_ERROR_NOTICE')
                          . '</span></div><div id="error_buttons" align="center"><br>'
                          . '<input type="button" value="'
                          . Loc::getMessage('FF_INST_RETRY_BUTTON')
                          . '" id="error_retry_button">&nbsp;<input type="button" id="error_skip_button" value="'
                          . Loc::getMessage('FF_INST_SKIP_BUTTON')
                          . '">&nbsp;</div></div>'
                          . $this->ShowHiddenField('nextStep', '')
                          . $this->ShowHiddenField('nextStepStage', '')
                          . '<iframe style="display: none;" id="iframe-post-form" '
                          . 'name="iframe-post-form" src="javascript:\'\'"></iframe>';

        $this->content .= '<script>var ajaxForm=new CAjaxForm("'
                          . $this->GetWizard()->GetFormName()
                          . '","iframe-post-form","'
                          . $this->GetWizard()->GetRealName('nextStep')
                          . '");ajaxForm.Post("0","skip","'
                          . Loc::getMessage('WIZ_START_INSTALL')
                          . '");</script>';
    }
}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */

/**
 * Class SuccessStep
 */
class SuccessStep extends \CWizardStep
{
    public function InitStep()
    {
        $this->SetStepID('success');

        $this->SetTitle(Loc::getMessage('WIZ_FINISH'));

        $this->SetNextStep('success');

        $this->SetNextCaption(Loc::getMessage('WIZ_GO'));
    }

    function OnPostForm()
    {
    }

    public function ShowStep()
    {
        $defaultDrawwwrIblockId = (int)\Bitrix\Main\Config\Option::get(
            DRAWWWR_MODULE_ID,
            'FF_DEFAULT_IBLOCK_ID'
        );

        if ($defaultDrawwwrIblockId > 0) {
            $documentRoot = Application::getDocumentRoot();

            \CopyDirFiles(
                "{$documentRoot}/_index.php",
                "{$documentRoot}/index.php",
                true,
                true,
                true
            );

            $component = '<' . '? ' . '$APPLICATION' . "->IncludeComponent(\n"
                         . "    'fostyfost:drawwwr',\n"
                         . "    '',\n"
                         . "    Array(\n"
                         . '        "CACHE_TIME" => "3600",' . "\n"
                         . '        "CACHE_TYPE" => "A",' . "\n"
                         . '        "COUNT" => "10",' . "\n"
                         . '        "SEF_FOLDER" => "/",' . "\n"
                         . '        "SEF_MODE" => "Y",' . "\n"
                         . '        "USE_COLOR_PICKER" => "N"' . "\n"
                         . '    ' . ")\n"
                         . '); ?' . '>';

            $indexPage = file_get_contents("{$documentRoot}/index.php");

            $indexPage = strtr(
                $indexPage,
                [
                    "#COMPONENT#" => $component,
                ]
            );

            file_put_contents("{$documentRoot}/index.php", $indexPage);

            \CMain::OnChangeFileComponent('/index.php', SITE_ID);
        }

        $this->GetWizard()->SetFormActionScript('/?finish');

        \bx_accelerator_reset();
    }
}
