<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Page\Asset;

global $APPLICATION;

Loader::registerAutoloadClasses(
    'zmkhitarian.testmodule',
    [
        'Zmkhitarian\\Testmodule\\IblockCustomProperty' => 'lib/IblockCustomProperty.php',
    ]
);
$moduleId = 'zmkhitarian.testmodule';
$curPage = $APPLICATION->GetCurPage();

if ((stripos($curPage, '/bitrix/admin/') !== false && stripos($curPage, 'iblock') !== false)
    || $APPLICATION->showPanelWasInvoked) {
    Asset::getInstance()->addJs('/local/js/' . $moduleId . '/iblockproperty.js');
}
