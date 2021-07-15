<?php

declare(strict_types=1);

namespace Pagination\Repository\TraitClass;

use Pagination\DTO\PaginatedArrayCollection;

trait PaginatedRepositoryFindByTrait
{
    /**
     * @param array|null $criteria
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     *
     * @return PaginatedArrayCollection
     */
    public function findByPaginate(?array $criteria = [], ?array $orderBy = null, ?int $limit = null, ?int $offset = null): PaginatedArrayCollection
    {
        if ($offset !== null && $limit !== null && $limit > 0) {
            $page = ceil($offset / $limit) + 1;
        } else {
            $page = 1;
        }

        return $this->findPageBy($page, $limit !== null && $limit > 0 ? $limit : -1, $criteria, $orderBy);
    }
}
