<?php

declare(strict_types=1);

namespace Pagination\Repository;

use Doctrine\ORM\EntityRepository;
use Pagination\Repository\TraitClass\FilterRepositoryTrait;
use Pagination\Repository\TraitClass\PaginatedRepositoryTrait;
use Pagination\Repository\TraitClass\PaginatedRepositoryFindByTrait;

class PaginatedRepository extends EntityRepository
{
    use PaginatedRepositoryTrait;
    use PaginatedRepositoryFindByTrait;
    use FilterRepositoryTrait;
}
