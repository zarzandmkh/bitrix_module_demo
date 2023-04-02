<?php

namespace Zmkhitarian\Testmodule;

use Bitrix\Iblock\IblockTable;
use Bitrix\Iblock\PropertyTable;
use Bitrix\Iblock\SectionTable;
use Exception;

class IblockCustomProperty
{
    /**
     * @return array
     */
    public function GetUserTypeDescription()
    {
        return array(
            'USER_TYPE_ID' => 'iblock_section',
            'USER_TYPE' => 'IBLOCK_SECTION_CUSTOM',
            'CLASS_NAME' => __CLASS__,
            'DESCRIPTION' => 'Выбор раздела другого инфоблока',
            'PROPERTY_TYPE' => PropertyTable::TYPE_STRING,
            'ConvertToDB' => [__CLASS__, 'ConvertToDB'],
            'ConvertFromDB' => [__CLASS__, 'ConvertFromDB'],
            'GetPropertyFieldHtml' => [__CLASS__, 'GetPropertyFieldHtml'],
        );
    }

    /**
     * @param $arProperty
     * @param $value
     * @return mixed
     */
    public static function ConvertToDB($arProperty, $value)
    {
        return $value;
    }

    /**
     * @param $arProperty
     * @param $value
     * @param $format
     * @return mixed
     * @throws Exception
     */
    public static function ConvertFromDB($arProperty, $value, $format = '')
    {
        return $value;
    }

    /**
     * @param $arProperty
     * @param $value
     * @param $arHtmlControl
     * @return string
     * @throws Exception
     */
    public static function GetPropertyFieldHtml($arProperty, $value, $arHtmlControl)
    {
        $valueSectionId = $value['VALUE'];
        $valueIblockId = self::getSectionIblockId($valueSectionId);

        $html = '';

        $iblocks = self::getIblockList();
        if (empty($iblocks)) {
            return $html;
        }

        $html .= self::getIblocksHtml($arProperty['ID'], $iblocks, $valueIblockId);

        if (!$valueSectionId || !$valueIblockId) {
            return $html;
        }

        $sections = self::getSectionList($valueIblockId);

        $html .= self::getSectionsHtml($arProperty['ID'], $sections, $valueSectionId);

        return $html;
    }

    /**
     * @param int $sectionId
     * @return int
     * @throws Exception
     */
    private static function getSectionIblockId(int $sectionId = 0): int
    {
        if (!$sectionId) {
            return 0;
        }

        $section = SectionTable::query()->where('ID', $sectionId)->addSelect('IBLOCK_ID')->fetchObject();

        return $section ? $section->getIblockId() : 0;
    }

    /**
     * @return array
     * @throws Exception
     */
    private static function getIblockList(): array
    {
        return IblockTable::query()->setSelect(['ID', 'NAME'])->fetchAll();
    }

    /**
     * @param $iblockId
     * @return array
     * @throws Exception
     */
    private static function getSectionList($iblockId): array
    {
        return SectionTable::query()->setSelect(['ID', 'NAME', 'IBLOCK_ID'])->setFilter(['IBLOCK_ID' => $iblockId]
        )->fetchAll();
    }

    /**
     * @param int $propertyId
     * @param array $iblocks
     * @param int|null $valueIblockId
     * @return string
     */
    private static function getIblocksHtml(int $propertyId, array $iblocks = [], ?int $valueIblockId = 0): string
    {
        if (!$iblocks) {
            return '';
        }

        $html = '<select class="zm_iblockselect" style="margin-right:10px;" data-propid="' . $propertyId . '">';
        $html .= '<option>Не выбрано</option>';

        foreach ($iblocks as $iblock) {
            $selected = $iblock['ID'] == $valueIblockId ? 'selected' : '';
            $html .= '<option value="' . $iblock['ID'] . '" ' . $selected . '>' . $iblock['NAME'] . '[' . $iblock['ID'] . ']</option>';
        }

        $html .= '</select>';

        return $html;
    }

    /**
     * @param int $propertyId
     * @param array $sections
     * @param int|null $valueSectionId
     * @return string
     */
    private static function getSectionsHtml(int $propertyId, array $sections = [], ?int $valueSectionId = 0): string
    {
        if (!$sections) {
            return '';
        }

        $html = '<select name="PROP[' . $propertyId . ']" class="zm_sectionselect">';

        foreach ($sections as $section) {
            $selected = $section['ID'] == $valueSectionId ? 'selected' : '';
            $value = $section['ID'];
            $html .= '<option value="' . htmlspecialchars(
                    json_encode($value)
                ) . '" ' . $selected . '>' . $section['NAME'] . '</option>';
        }

        return $html;
    }
}
