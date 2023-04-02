<?php

use Bitrix\Iblock\IblockTable;
use Bitrix\Main\Loader;

try {
    Loader::includeModule('iblock');
    $arIblocks = [];
    $iblocks = IblockTable::query()->setSelect(['ID', 'NAME'])->fetchCollection();
    foreach ($iblocks as $iblock) {
        $arIblocks[$iblock->getId()] = $iblock->getName() . '[' . $iblock->getId() . ']';
    }
} catch (Exception $e) {
    throw new Exception($e->getMessage());
}

return [
    'edit1' => [
        'TAB_NAME' => 'Настройки',
        'TAB_TITLE' => 'Настройки',
        'ICON' => '',
        'options' => [
            [
                'type' => 'list',
                'name' => 'reviews_iblock_id',
                'title' => 'Нифоблок отзывов',
                'list' => $arIblocks,
            ],
        ]
    ],
];
