<?php

declare(strict_types=1);

namespace Pagination\Repository\TraitClass;

use Doctrine\ORM\QueryBuilder;

interface FilterRepositoryInterface
{
    public function buildFilterCriteria(QueryBuilder $qb, array $criteria): void;
}
