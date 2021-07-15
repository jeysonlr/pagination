<?php

declare(strict_types=1);

namespace Pagination\Service;

abstract class QueryParamsValidation
{
    /**
     * @param array $queryParams
     * @return array
     */
    public static function validate(array $queryParams): array
    {
        $page        = isset($queryParams['page']) ? $queryParams['page'] : 1;
        $perPage    = isset($queryParams['limit']) ? $queryParams['limit'] : 20;
        $searchField = isset($queryParams['searchfield']) ? $queryParams['searchfield'] : '';
        $search      = isset($queryParams['search']) ? $queryParams['search'] : '';
        $orderBy     = isset($queryParams['order']) ? $queryParams['order'] : '';
        $sort        = isset($queryParams['sort']) ? $queryParams['sort'] : '';

        return [
            "queryparams"  => $queryParams,
            "page"         => $page,
            "limit"        => $perPage,
            "searchfield" => $searchField,
            "search"       => $search,
            "order"      => $orderBy,
            "sort"         => $sort
        ];
    }
}
