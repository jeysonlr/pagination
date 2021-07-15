<?php

declare(strict_types=1);

namespace Pagination\Helper;

use Pagination\Repository\TraitClass\FilterRepositoryTrait;

class FilteredRepositoryExample
{
    use FilterRepositoryTrait;

    /**
     * @var string
     */
    protected $entityAlias;

    /**
     * @return string
     */
    public function getEntityAlias(): string
    {
        return $this->entityAlias;
    }

    /**
     * @param string $entityAlias
     */
    public function setEntityAlias(string $entityAlias): void
    {
        $this->entityAlias = $entityAlias;
    }
}
