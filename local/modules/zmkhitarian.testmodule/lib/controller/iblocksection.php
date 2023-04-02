<?php

namespace Zmkhitarian\Testmodule\Controller;

use Bitrix\Iblock\SectionTable;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Loader;
use Bitrix\Main\Request;
use Exception;

class IblockSection extends Controller
{
    /**
     * @param Request|null $request
     * @throws Exception
     */
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        Loader::includeModule('iblock');
    }

    /**
     * @param $iblockId
     * @return array|null
     * @throws Exception
     */
    public function getListAction($iblockId): ?array
    {
        return SectionTable::query()
            ->setSelect(['ID', 'NAME'])
            ->setFilter(['IBLOCK_ID' => $iblockId])
            ->fetchAll();
    }
}
