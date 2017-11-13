<?php

use \Bitrix\Main\Application;
use \Bitrix\Main\Loader;
use \Bitrix\Main\SiteTable;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\ModuleManager;
use \Bitrix\Main\EventManager;
use \Bitrix\Main\SystemException;
use \Bitrix\Iblock;
use \FostyFost\Drawwwr\Model\DrawwwrFilesTable;

if (!defined('DRAWWWR_TYPE')) {
    define('DRAWWWR_TYPE', 'drawwwr');
}

if (class_exists('fostyfost_drawwwr')) {
    return;
}

Loc::loadMessages(__FILE__);

/**
 * Class fostyfost_drawwwr
 */
class fostyfost_drawwwr extends \CModule
{
    /** @var string $MODULE_ID */
    public $MODULE_ID = 'fostyfost.drawwwr';

    /** @var mixed $MODULE_VERSION */
    public $MODULE_VERSION;

    /** @var mixed $MODULE_VERSION_DATE */
    public $MODULE_VERSION_DATE;

    /** @var mixed|string $MODULE_NAME */
    public $MODULE_NAME;

    /** @var mixed|string $MODULE_DESCRIPTION */
    public $MODULE_DESCRIPTION;

    /** @var mixed|string $PARTNER_NAME */
    public $PARTNER_NAME;

    /** @var mixed|string $PARTNER_URI */
    public $PARTNER_URI;

    /** @var int $drawwwrIblockId */
    private $drawwwrIblockId;

    /** @var int $drawwwrPropertyImage */
    private $drawwwrPropertyImage;

    /** @var int $drawwwrPropertyH5i */
    private $drawwwrPropertyH5i;

    /**
     * fostyfost_drawwwr constructor
     */
    public function __construct()
    {
        $arModuleVersion = [];

        include(__DIR__ . '/version.php');

        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];

            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }

        $this->MODULE_NAME = Loc::getMessage('FF_MODULE_NAME');

        $this->MODULE_DESCRIPTION = Loc::getMessage('FF_MODULE_DESCRIPTION');

        $this->PARTNER_NAME = Loc::getMessage('FF_PARTNER_NAME');

        $this->PARTNER_URI = Loc::getMessage('FF_PARTNER_URI');
    }

    /**
     * @param array $arParams
     *
     * @return bool
     */
    public function installEvents($arParams = [])
    {
        $eventManager = EventManager::getInstance();

        /** @see \FostyFost\Drawwwr\Debug\Events::buildDebugMenu */
        $eventManager->registerEventHandlerCompatible(
            'main',
            'OnBuildGlobalMenu',
            $this->MODULE_ID,
            '\\FostyFost\\Drawwwr\\Debug\\Events',
            'buildDebugMenu'
        );

        /** @see \FostyFost\Drawwwr\Debug\Events::initDebug */
        $eventManager->registerEventHandlerCompatible(
            'main',
            'OnBeforeProlog',
            $this->MODULE_ID,
            '\\FostyFost\\Drawwwr\\Debug\\Events',
            'initDebug'
        );

        /** @see \FostyFost\Drawwwr\Debug\Events::showDebugContent */
        $eventManager->registerEventHandlerCompatible(
            'main',
            'OnProlog',
            $this->MODULE_ID,
            '\\FostyFost\\Drawwwr\\Debug\\Events',
            'showDebugContent'
        );

        /** @see \FostyFost\Drawwwr\Events::onBeforeEdit */
        $eventManager->registerEventHandler(
            $this->MODULE_ID,
            'onBeforeEdit',
            $this->MODULE_ID,
            '\\FostyFost\\Drawwwr\\Events',
            'onBeforeEdit'
        );

        return true;
    }

    /**
     * @param array $arParams
     *
     * @return bool
     */
    public function unInstallEvents($arParams = [])
    {
        $eventManager = EventManager::getInstance();

        /** @see \FostyFost\Drawwwr\Debug\Events::buildDebugMenu */
        $eventManager->unRegisterEventHandler(
            'main',
            'OnBuildGlobalMenu',
            $this->MODULE_ID,
            '\\FostyFost\\Drawwwr\\Debug\\Events',
            'buildDebugMenu'
        );

        /** @see \FostyFost\Drawwwr\Debug\Events::initDebug */
        $eventManager->unRegisterEventHandler(
            'main',
            'OnBeforeProlog',
            $this->MODULE_ID,
            '\\FostyFost\\Drawwwr\\Debug\\Events',
            'initDebug'
        );

        /** @see \FostyFost\Drawwwr\Debug\Events::showDebugContent */
        $eventManager->unRegisterEventHandler(
            'main',
            'OnProlog',
            $this->MODULE_ID,
            '\\FostyFost\\Drawwwr\\Debug\\Events',
            'showDebugContent'
        );

        /** @see \FostyFost\Drawwwr\Events::onBeforeEdit */
        $eventManager->unRegisterEventHandler(
            $this->MODULE_ID,
            'onBeforeEdit',
            $this->MODULE_ID,
            '\\FostyFost\\Drawwwr\\Events',
            'onBeforeEdit'
        );

        return true;
    }

    /**
     * @param array $arParams
     *
     * @return bool
     */
    public function installFiles($arParams = [])
    {
        $documentRoot = Application::getDocumentRoot();

        $path = "{$documentRoot}/local/modules/{$this->MODULE_ID}/admin";

        if (is_dir($path)) {
            if ($dir = opendir($path)) {
                while (false !== $item = readdir($dir)) {
                    if ($item === '..' || $item === '.' || $item === 'menu.php') {
                        continue;
                    }

                    file_put_contents(
                        $file = "{$documentRoot}/bitrix/admin/{$this->MODULE_ID}_{$item}",
                        '<'
                        . "? require(\$_SERVER[\"DOCUMENT_ROOT\"] . '/local/modules/{$this->MODULE_ID}"
                        . "/admin/{$item}'); ?"
                        . '>'
                    );
                }

                closedir($dir);
            }
        }

        \CopyDirFiles(
            "{$documentRoot}/local/modules/{$this->MODULE_ID}/install/wizards/fostyfost/drawwwr/",
            "{$documentRoot}/bitrix/wizards/fostyfost/drawwwr/",
            true,
            true
        );

        \CopyDirFiles(
            "{$documentRoot}/local/modules/{$this->MODULE_ID}/install/images/",
            "{$documentRoot}/bitrix/images/{$this->MODULE_ID}/",
            true,
            true
        );

        \CopyDirFiles(
            "{$documentRoot}/local/modules/{$this->MODULE_ID}/install/js/",
            "{$documentRoot}/bitrix/js/{$this->MODULE_ID}/",
            true,
            true
        );

        \CopyDirFiles(
            "{$documentRoot}/local/modules/{$this->MODULE_ID}/install/themes/",
            "{$documentRoot}/bitrix/themes/",
            true,
            true
        );

        \CopyDirFiles(
            "{$documentRoot}/local/modules/{$this->MODULE_ID}/install/components",
            "{$documentRoot}/local/components/",
            true,
            true
        );

        return true;
    }

    /**
     * @return bool
     */
    public function unInstallFiles()
    {
        $documentRoot = Application::getDocumentRoot();

        $path = "{$documentRoot}/local/modules/{$this->MODULE_ID}/admin";

        if (is_dir($path)) {
            if ($dir = opendir($path)) {
                while (false !== $item = readdir($dir)) {
                    if ($item === '..' || $item === '.') {
                        continue;
                    }

                    unlink("{$documentRoot}/bitrix/admin/{$this->MODULE_ID}_{$item}");
                }

                closedir($dir);
            }
        }

        \DeleteDirFilesEx('/bitrix/wizards/fostyfost/drawwwr/');

        \DeleteDirFilesEx("/local/components/fostyfost/");

        \DeleteDirFilesEx("/bitrix/images/{$this->MODULE_ID}/");

        \DeleteDirFilesEx("/bitrix/js/{$this->MODULE_ID}/");

        \DeleteDirFilesEx("/bitrix/themes/.default/{$this->MODULE_ID}/");

        \DeleteDirFilesEx("/bitrix/themes/.default/icons/{$this->MODULE_ID}/");

        \DeleteDirFiles(
            "{$documentRoot}/local/modules/{$this->MODULE_ID}/install/themes/.default/",
            "{$documentRoot}/bitrix/themes/.default"
        );

        return true;
    }

    /**
     * @return string
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     */
    private function createIblock()
    {
        $defaultSite = SiteTable::getList([
            "select" => ["LID"],
            "filter" => ["=DEF" => 'Y']
        ])->fetch();

        $siteId = [];

        if (is_array($defaultSite) && !empty($defaultSite)) {
            $siteId = [$defaultSite["LID"]];
        }

        /**
         * Описываем свойства нового типа инфоблока
         */
        $arFieldsIblockType = [
            "ID"       => DRAWWWR_TYPE,
            "SECTIONS" => 'N',
            "IN_RSS"   => 'N',
            "SORT"     => 1,
            "LANG"     => [
                "ru" => [
                    "NAME"         => Loc::getMessage('FF_IBLOCK_TYPE_DRAWWWR_NAME_RU'),
                    "SECTION_NAME" => Loc::getMessage('FF_IBLOCK_TYPE_DRAWWWR_SECTION_NAME_RU'),
                    "ELEMENT_NAME" => Loc::getMessage('FF_IBLOCK_TYPE_DRAWWWR_ELEMENT_NAME_RU')
                ],
                "en" => [
                    "NAME"         => Loc::getMessage('FF_IBLOCK_TYPE_DRAWWWR_NAME_EN'),
                    "SECTION_NAME" => Loc::getMessage('FF_IBLOCK_TYPE_DRAWWWR_SECTION_NAME_EN'),
                    "ELEMENT_NAME" => Loc::getMessage('FF_IBLOCK_TYPE_DRAWWWR_ELEMENT_NAME_EN')
                ],
            ]
        ];

        /**
         * Описываем свойства нового инфоблока
         */
        $arFieldsIblock = [
            "VERSION"          => 1,
            "ACTIVE"           => 'Y',
            "NAME"             => Loc::getMessage('FF_IBLOCK_DRAWWWR_NAME'),
            "IBLOCK_TYPE_ID"   => DRAWWWR_TYPE,
            "CODE"             => DRAWWWR_TYPE,
            "SITE_ID"          => $siteId,
            "SORT"             => 1,
            "GROUP_ID"         => [2 => 'R'],
            "LIST_MODE"        => 'C',
            "WORKFLOW"         => 'N',
            "INDEX_ELEMENT"    => 'N',
            "INDEX_SECTION"    => 'N',
            "RSS_ACTIVE"       => 'N',
            "XML_ID"           => DRAWWWR_TYPE,
            "LIST_PAGE_URL"    => '/#IBLOCK_CODE#/',
            "SECTION_PAGE_URL" => '/#IBLOCK_CODE#/#SECTION_CODE_PATH#/',
            "DETAIL_PAGE_URL"  => '/#IBLOCK_CODE#/#SECTION_CODE_PATH#/#ELEMENT_ID#/',
            "FIELDS"           => [
                "ACTIVE_FROM"    => [
                    "IS_REQUIRED"   => 'N',
                    "DEFAULT_VALUE" => ''
                ],
                "ACTIVE_TO"      => [
                    "IS_REQUIRED"   => 'N',
                    "DEFAULT_VALUE" => ''
                ],
                "DETAIL_PICTURE" => [
                    "IS_REQUIRED" => 'N'
                ],
                "CODE"           => [
                    "IS_REQUIRED"   => 'N',
                    "DEFAULT_VALUE" => ["UNIQUE" => 'Y']
                ],
                "IBLOCK_SECTION" => [
                    "IS_REQUIRED" => 'N'
                ],
                "SECTION_CODE"   => [
                    "IS_REQUIRED"   => 'N',
                    "DEFAULT_VALUE" => [
                        "TRANSLITERATION" => 'Y',
                        "UNIQUE"          => 'Y',
                        "TRANS_CASE"      => 'L',
                        "TRANS_SPACE"     => '-',
                        "TRANS_OTHER"     => '-'
                    ]
                ]
            ],
            "IS_CATALOG"       => 'N',
            "VAT_ID"           => ''
        ];

        /**
         * Описываем поля свойств нового нужных инфоблоков
         */
        $arProperties = [
            "PROPERTY_IMAGE" => [
                "NAME"          => Loc::getMessage('FF_PROPERTY_IMAGE'),
                "ACTIVE"        => 'Y',
                "SORT"          => 1,
                "CODE"          => 'IMAGE',
                "IBLOCK_ID"     => '{NEW_IBLOCK_ID}',
                "PROPERTY_TYPE" => 'F',
                "IS_REQUIRED"   => 'Y',
                "FILTRABLE"     => 'N',
                "FILE_TYPE"     => 'png'
            ],
            "PROPERTY_H5I"   => [
                "NAME"          => Loc::getMessage('FF_PROPERTY_H5I'),
                "ACTIVE"        => 'Y',
                "SORT"          => 2,
                "CODE"          => 'H5I',
                "IBLOCK_ID"     => '{NEW_IBLOCK_ID}',
                "PROPERTY_TYPE" => 'F',
                "IS_REQUIRED"   => 'Y',
                "FILTRABLE"     => 'N',
                "FILE_TYPE"     => 'h5i'
            ]
        ];

        global $APPLICATION, $DB;

        $DB->StartTransaction();

        $info = [];

        try {
            Loader::includeModule('iblock');

            // {{{ Add type
            $boolIblockExists = false;

            $iblockTypeIterator = Iblock\TypeTable::getById(DRAWWWR_TYPE);

            $iblockType = $iblockTypeIterator->fetch();

            if ($iblockType) {
                $boolIblockExists = true;
            }

            $obBlockType = new \CIBlockType;

            if ($boolIblockExists) {
                if ($obBlockType->Update($arFieldsIblockType["ID"], $arFieldsIblockType)) {
                    $info[] = Loc::getMessage(
                        'FF_NEW_IBLOCK_TYPE_MESSAGE_UPDATE',
                        ["#IBLOCK_TYPE#" => $arFieldsIblockType["LANG"]["ru"]["NAME"]]
                    );
                } else {
                    throw new SystemException(
                        Loc::getMessage(
                            'FF_NEW_IBLOCK_TYPE_MESSAGE_ERROR_UPDATE',
                            ["#ERROR#" => $obBlockType->LAST_ERROR]
                        )
                    );
                }
            } else {
                $res = $obBlockType->Add($arFieldsIblockType);

                if ($res) {
                    $info[] = Loc::getMessage(
                        'FF_NEW_IBLOCK_TYPE_MESSAGE_ADDED',
                        ["#IBLOCK_TYPE#" => $arFieldsIblockType["LANG"]["ru"]["NAME"]]
                    );
                } else {
                    throw new SystemException(
                        Loc::getMessage(
                            'FF_NEW_IBLOCK_TYPE_MESSAGE_ERROR',
                            ["#ERROR#" => $obBlockType->LAST_ERROR]
                        )
                    );
                }
            }
            // }}}

            // {{{ Add iblock
            $newIBlockId = 0;

            $iblockIterator = Iblock\IblockTable::getList([
                "filter" => ["=IBLOCK_TYPE_ID" => DRAWWWR_TYPE]
            ]);

            $iblock = $iblockIterator->fetch();

            if ($iblock) {
                $newIBlockId = $iblock["ID"];
            }

            $obIBlock = new \CIBlock;

            $newIBlockId = (int)$newIBlockId;

            if ($newIBlockId > 0) {
                $this->drawwwrIblockId = $newIBlockId;

                if ($obIBlock->Update($newIBlockId, $arFieldsIblock)) {
                    $info[] = Loc::getMessage(
                        'FF_NEW_IBLOCK_MESSAGE_UPDATE',
                        [
                            "#IBLOCK#" => $arFieldsIblock["NAME"],
                            "#ID#"     => $newIBlockId
                        ]
                    );
                } else {
                    throw new SystemException(
                        Loc::getMessage(
                            'FF_NEW_IBLOCK_MESSAGE_ERROR_UPDATE',
                            ["#ERROR#" => $obIBlock->LAST_ERROR]
                        )
                    );
                }
            } else {
                $res = $obIBlock->Add($arFieldsIblock);

                if ($res) {
                    $this->drawwwrIblockId = $newIBlockId = (int)$res;

                    $info[] = Loc::getMessage(
                        'FF_NEW_IBLOCK_MESSAGE_ADDED',
                        [
                            "#IBLOCK#" => $arFieldsIblock["NAME"],
                            "#ID#"     => $newIBlockId
                        ]
                    );
                } else {
                    throw new SystemException(
                        Loc::getMessage(
                            'FF_NEW_IBLOCK_MESSAGE_ERROR',
                            ["#ERROR#" => $obIBlock->LAST_ERROR]
                        )
                    );
                }
            }
            // }}}

            // {{{ Add Iblock Props
            foreach ($arProperties as $propertyKey => $arProperty) {
                $arProperty["IBLOCK_ID"] = str_replace(
                    '{NEW_IBLOCK_ID}',
                    $newIBlockId,
                    $arProperty["IBLOCK_ID"]
                );

                $ibp = new \CIBlockProperty;

                $resProperty = \CIBlockProperty::GetList(
                    [],
                    [
                        "CODE"      => $arProperty["CODE"],
                        "IBLOCK_ID" => $arProperty["IBLOCK_ID"]
                    ]
                );

                $arHasProperty = $resProperty->Fetch();

                if ($arHasProperty) {
                    if ($ibp->Update($arHasProperty["ID"], $arProperty)) {
                        if ($propertyKey === 'PROPERTY_IMAGE') {
                            $this->drawwwrPropertyImage = (int)$arHasProperty["ID"];
                        } elseif ($propertyKey === 'PROPERTY_H5I') {
                            $this->drawwwrPropertyH5i = (int)$arHasProperty["ID"];
                        }

                        $info[] = Loc::getMessage(
                            'FF_NEW_IBLOCK_PROP_MESSAGE_UPDATE',
                            [
                                "#NAME#" => $arProperty["NAME"],
                                "#ID#"   => $arHasProperty["ID"]
                            ]
                        );
                    } else {
                        $ex = $APPLICATION->GetException();

                        if ($ex) {
                            throw new SystemException(
                                Loc::getMessage(
                                    'FF_NEW_IBLOCK_PROP_MESSAGE_UPDATE_ERROR',
                                    [
                                        "#ERROR#" => $ex->GetString(),
                                        "#NAME#"  => $arProperty["NAME"],
                                        "#ID#"    => $arHasProperty["ID"]
                                    ]
                                )
                            );
                        } elseif (isset($ibp->LAST_ERROR)) {
                            throw new SystemException(
                                Loc::getMessage(
                                    'FF_NEW_IBLOCK_PROP_MESSAGE_UPDATE_ERROR',
                                    [
                                        "#ERROR#" => $ibp->LAST_ERROR,
                                        "#NAME#"  => $arProperty["NAME"],
                                        "#ID#"    => $arHasProperty["ID"]
                                    ]
                                )
                            );
                        }
                    }
                } else {
                    $propId = $ibp->Add($arProperty);

                    if ($propId) {
                        $info[] = Loc::getMessage(
                            'FF_NEW_IBLOCK_PROP_MESSAGE_ADDED',
                            [
                                "#NAME#" => $arProperty["NAME"],
                                "#ID#"   => $propId
                            ]
                        );

                        if ($propertyKey === 'PROPERTY_IMAGE') {
                            $this->drawwwrPropertyImage = (int)$propId;
                        } elseif ($propertyKey === 'PROPERTY_H5I') {
                            $this->drawwwrPropertyH5i = (int)$propId;
                        }
                    } else {
                        $ex = $APPLICATION->GetException();

                        if ($ex) {
                            throw new SystemException(
                                Loc::getMessage(
                                    'FF_NEW_IBLOCK_PROP_MESSAGE_ADDED_ERROR',
                                    [
                                        "#NAME#"  => $arProperty["NAME"],
                                        "#ERROR#" => $ex->GetString()
                                    ]
                                )
                            );
                        } elseif (isset($ibp->LAST_ERROR)) {
                            throw new SystemException(
                                Loc::getMessage(
                                    'FF_NEW_IBLOCK_PROP_MESSAGE_ADDED_ERROR',
                                    [
                                        "#NAME#"  => $arProperty["NAME"],
                                        "#ERROR#" => $ibp->LAST_ERROR
                                    ]
                                )
                            );
                        }
                    }
                }
            }

            $DB->Commit();

            return implode("<br>\n", $info);
        } catch (SystemException $e) {
            $DB->Rollback();

            return sprintf("%s<br>\n%s", $e->getMessage(), implode("<br>\n", $info));
        }
    }

    /**
     * @throws \Bitrix\Main\LoaderException
     */
    public function installDb()
    {
        if (!Loader::includeModule($this->MODULE_ID)) {
            return;
        }

        $tableName = DrawwwrFilesTable::getTableName();

        if (Application::getConnection()->isTableExists($tableName)) {
            return;
        }

        DrawwwrFilesTable::getEntity()->createDbTable();
    }

    /**
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\Db\SqlQueryException
     * @throws \Bitrix\Main\LoaderException
     */
    public function uninstallDb()
    {
        if (!Loader::includeModule($this->MODULE_ID)) {
            return;
        }

        $tableName = DrawwwrFilesTable::getTableName();

        $connection = Application::getConnection();

        if ($connection->isTableExists($tableName)) {
            $connection->dropTable($tableName);
        }
    }

    /**
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     * @throws \Bitrix\Main\LoaderException
     */
    public function doInstall()
    {
        if (!\check_bitrix_sessid()) {
            return;
        }

        global $APPLICATION, $iblockCreationLog;

        if (version_compare(SM_VERSION, '17.5.0') < 0) {
            $APPLICATION->IncludeAdminFile(
                Loc::getMessage('FF_INSTALL_TITLE'),
                __DIR__ . '/install_unsuccessful.php'
            );

            return;
        }

        ModuleManager::registerModule($this->MODULE_ID);

        $this->installFiles();

        $this->installEvents();

        $this->installDb();

        Option::set($this->MODULE_ID, 'FF_DEBUG_JQUERY', 'Y');

        Option::set($this->MODULE_ID, 'FF_DEBUG_GROUPS', '1');

        $iblockCreationLog = $this->createIblock();

        $drawwwrIblockId = (int)$this->drawwwrIblockId;

        if ($drawwwrIblockId > 0) {
            Option::set($this->MODULE_ID, 'FF_DEFAULT_IBLOCK_ID', $drawwwrIblockId);
        }

        $drawwwrPropertyImage = (int)$this->drawwwrPropertyImage;

        if ($drawwwrPropertyImage > 0) {
            Option::set($this->MODULE_ID, 'FF_DEFAULT_PROPERTY_IMAGE', $drawwwrPropertyImage);
        }

        $drawwwrPropertyH5i = (int)$this->drawwwrPropertyH5i;

        if ($drawwwrPropertyH5i > 0) {
            Option::set($this->MODULE_ID, 'FF_DEFAULT_PROPERTY_H5I', $drawwwrPropertyH5i);
        }

        $APPLICATION->IncludeAdminFile(
            Loc::getMessage('FF_INSTALL_TITLE'),
            __DIR__ . '/install_successful.php'
        );
    }

    /**
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     * @throws \Bitrix\Main\Db\SqlQueryException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Exception
     */
    public function doUninstall()
    {
        if (!\check_bitrix_sessid()) {
            return;
        }

        $this->unInstallFiles();

        $this->unInstallEvents();

        $this->uninstallDb();

        if (Loader::includeModule('iblock')) {
            \CIBlock::Delete(Option::get($this->MODULE_ID, 'FF_DEFAULT_IBLOCK_ID'));

            \CIBlockType::Delete(DRAWWWR_TYPE);
        }

        Option::delete($this->MODULE_ID, ["name" => 'FF_DEBUG_JQUERY']);

        Option::delete($this->MODULE_ID, ["name" => 'FF_DEBUG_GROUPS']);

        Option::delete($this->MODULE_ID, ["name" => 'FF_DEFAULT_IBLOCK_ID']);

        Option::delete($this->MODULE_ID, ["name" => 'FF_DEFAULT_PROPERTY_IMAGE']);

        Option::delete($this->MODULE_ID, ["name" => 'FF_DEFAULT_PROPERTY_H5I']);

        ModuleManager::unRegisterModule($this->MODULE_ID);
    }
}
