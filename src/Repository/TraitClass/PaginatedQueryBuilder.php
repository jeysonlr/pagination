<?php

declare(strict_types=1);

namespace Pagination\Repository\TraitClass;

use Doctrine\ORM\QueryBuilder;

class PaginatedQueryBuilder extends QueryBuilder
{
    /**
     * @param array $orderBy
     * @param null  $entityAlias
     * @return PaginatedQueryBuilder
     */
    public function addOrder(array $orderBy, $entityAlias = null): PaginatedQueryBuilder
    {
        foreach ($orderBy as $field => $order) {
            if (preg_match('/^[a-z0-9][a-z0-9\_]+$/i', $field)) {
                $this->addOrderBy(($entityAlias ? $entityAlias . '.' : '') . $field, $order);
            } else {
                $this->addOrderBy($field, $order);
            }
        }

        return $this;
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return PaginatedQueryBuilder
     */
    public function addPagination(int $page, int $perPage): PaginatedQueryBuilder
    {
        $offset = ($page - 1) * $perPage;
        $limit = $perPage;

        $this->setFirstResult($offset);
        $this->setMaxResults($limit);

        return $this;
    }
}