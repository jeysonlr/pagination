<?php

declare(strict_types=1);

namespace Pagination\Builder;

use Throwable;
use Exception;
use Doctrine\ORM\Query;
use Pagination\DTO\Params;

abstract class MapperParamsDto
{
    const NUMBER_ONE = 1, NUMBER_TWENTY = 20, HYDRATE_OBJECT = Query::HYDRATE_OBJECT;

    /**
     * @param array $params
     * @param int $hydrationMode
     * @return Params
     * @throws Exception
     */
    public static function map(array $params, int $hydrationMode = self::HYDRATE_OBJECT): Params
    {
        try {
            $page = intval($params["page"]);
            $limit = intval($params["limit"]);

            return (new Params())
                ->setPage(($page < self::NUMBER_ONE) ? self::NUMBER_ONE : $page)
                ->setPerPage(($limit < self::NUMBER_ONE) ? self::NUMBER_TWENTY : $limit)
                ->setHydrateMode($hydrationMode)
                ->setSearchField($params["searchfield"])
                ->setSearch($params["search"])
                ->setOrder($params["order"])
                ->setSort($params["sort"]);
        } catch (Throwable $e) {
            throw new Exception(
                "Erro ao tentar mapear DTO de parâmetros",
                500
            );
        }
    }
}
