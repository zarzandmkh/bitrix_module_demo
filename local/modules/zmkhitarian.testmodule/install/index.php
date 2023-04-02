<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);

if (class_exists('zmkhitarian_testmodule')) {
    return;
}

class zmkhitarian_testmodule extends CModule
{
    public $MODULE_ID = 'zmkhitarian.testmodule';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $MODULE_GROUP_RIGHTS = 'Y';


    public function __construct()
    {
        $arModuleVersion = [];

        require __DIR__ . '/version.php';

        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];

        $this->MODULE_NAME = "Тестовый модуль";
        $this->MODULE_DESCRIPTION = "Тестовый модуль";

        $this->PARTNER_NAME = "Zarzand Mkhitaryan";
        $this->PARTNER_URI = "zmkhitaryan88@gmail.com";
    }

    /**
     * @return void
     */
    public function DoInstall()
    {
        global $APPLICATION;
        $this->InstallEvents();
        $this->InstallFiles();
        ModuleManager::registerModule($this->MODULE_ID);
    }

    /**
     * @return void
     */
    public function DoUninstall()
    {
        global $APPLICATION, $step, $obModule;

        if ($step < 2) {
            $APPLICATION->IncludeAdminFile(GetMessage("Удаление модуля"), __DIR__ . '/unstep1.php');
        } elseif ($step == 2) {
            $GLOBALS['CACHE_MANAGER']->CleanAll();
            ModuleManager::unRegisterModule($this->MODULE_ID);

            $this->UnInstallEvents();
            $this->UnInstallFiles();

            $obModule = $this;
            $APPLICATION->IncludeAdminFile(GetMessage("Удаление модуля"), __DIR__ . '/unstep2.php');
        }
    }

    /**
     * @return true
     */
    public function InstallEvents()
    {
        RegisterModuleDependences(
            'iblock',
            'OnIBlockPropertyBuildList',
            $this->MODULE_ID,
            'Zmkhitarian\\Testmodule\\IblockCustomProperty',
            'GetUserTypeDescription'
        );
        return true;
    }

    /**
     * @return true
     */
    public function UnInstallEvents()
    {
        UnRegisterModuleDependences(
            'iblock',
            'OnIBlockPropertyBuildList',
            $this->MODULE_ID,
            'Zmkhitarian\\Testmodule\\IblockCustomProperty',
            'GetUserTypeDescription'
        );
        return true;
    }

    /**
     * @return true
     */
    function InstallFiles()
    {
        CopyDirFiles(
            __DIR__ . '/js/',
            $_SERVER['DOCUMENT_ROOT'] . '/local/js/' . $this->MODULE_ID,
            true,
            true
        );
        return true;
    }

    /**
     * @return true
     */
    function UnInstallFiles()
    {
        DeleteDirFilesEx('/local/js/' . $this->MODULE_ID . '/');
        return true;
    }
}
