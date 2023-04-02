<?php

namespace Zmkhitarian\Testmodule\Controller;

use Bitrix\Iblock\Iblock;
use Bitrix\Iblock\SectionTable;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Loader;
use Bitrix\Main\Request;
use Bitrix\Main\UI\PageNavigation;
use Exception;

class Reviews extends Controller
{
    const MODULE_ID = 'zmkhitarian.testmodule';
    const CITY_PROPERTY_CODE = 'TEST';
    const RATING_PROPERTY_CODE = 'RATING';
    const DEFAULT_LIMIT = 10;

    public ?PageNavigation $pageNavigation;

    /**
     * @param Request|null $request
     * @throws Exception
     */
    public function __construct(Request $request = null)
    {
        parent::__construct($request);

        if (!Loader::includeModule('iblock')) {
            throw new Exception('Модуль iblock не подключен');
        }

        $this->pageNavigation = new PageNavigation('reviews');
    }

    /**
     * @param int $page
     * @param int $limit
     * @return array
     * @throws Exception
     */
    public function getListAction(int $page = 1, int $limit = 0): array
    {
        global $USER;

        if (!$USER->IsAuthorized()) {
            throw new Exception('Вы не авторизованы', 401);
        }

        $iblockId = $this->getIblockId();
        $iblockCLass = Iblock::wakeUp($iblockId)->getEntityDataClass();

        $nav = $this->getPageNavigation();
        $nav->setPageSize($limit ?: self::DEFAULT_LIMIT);
        $nav->setCurrentPage($page ?: 0);
        $nav->initFromUri();

        $query = $iblockCLass::query()
            ->setSelect([
                'ID',
                'NAME',
                'CITY_SECTION_ID' => self::CITY_PROPERTY_CODE . '.VALUE',
                self::RATING_PROPERTY_CODE . '.VALUE',
                'CITY',
            ])
            ->registerRuntimeField(
                'CITY',
                [
                    'data_type' => SectionTable::class,
                    'reference' => [
                        'ref.ID' => 'this.CITY_SECTION_ID'
                    ]
                ]
            )
            ->setOffset($nav->getOffset())
            ->setLimit($nav->getLimit())
            ->countTotal(true)
            ->exec();

        $countAll = $query->getCount();

        $nav->setRecordCount($countAll);
        $reviews = $query->fetchCollection();

        $arReviews = [];

        foreach ($reviews as $review) {
            $city = $review->get('CITY');
            $arReviews[] = [
                'fields' => [
                    'id' => $review->getId(),
                    'name' => $review->getName(),
                ],
                'properties' => [
                    'city' => $city ? $city->getName() : '',
                    'rating' => intval($review->get(self::RATING_PROPERTY_CODE))
                ]
            ];
        }

        return [
            'list' => $arReviews,
            'all_count' => $countAll
        ];
    }

    /**
     * @return PageNavigation
     */
    private function getPageNavigation(): PageNavigation
    {
        return $this->pageNavigation;
    }

    /**
     * @return int|null
     * @throws Exception
     */
    public function getIblockId(): ?int
    {
        if (!$iblockId = Option::get(self::MODULE_ID, 'reviews_iblock_id')) {
            throw new Exception('Не задан id нифоблока');
        }
        return $iblockId;
    }
}
