<?php

declare(strict_types=1);

namespace Pagination\Repository\TraitClass;

use Pagination\DTO\Params;
use Doctrine\ORM\NativeQuery;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\NonUniqueResultException;
use Pagination\DTO\PaginatedArrayCollection;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

trait PaginatedRepositoryTrait
{
    /**
     * @param Params $params
     * @return PaginatedArrayCollection
     */
    public function findPageWithDTO(Params $params): PaginatedArrayCollection
    {
        return $this->findPageBy($params);
    }

    /**
     * @param NativeQuery $query
     *
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countSql(NativeQuery $query): int
    {
        $sqlInitial = $query->getSQL();
        $rsm = new ResultSetMappingBuilder($query->getEntityManager());
        $rsm->addScalarResult('count', 'count');

        $sqlCount = 'select count(*) as count from (' . $sqlInitial . ') as item';
        $qCount = $query->getEntityManager()->createNativeQuery($sqlCount, $rsm);
        $qCount->setParameters($query->getParameters());

        return (int)$qCount->getSingleScalarResult();
    }

    /**
     * @param NativeQuery $query
     * @param int $page
     * @param int $perPage
     *
     * @return array
     */
    public function manageSqlParams(NativeQuery $query, int $page, int $perPage): array
    {
        $query->setSQL($query->getSQL() . ' offset ' . (($page - 1) * $perPage) . ' limit ' . $perPage);
        return $query->getArrayResult();
    }

    /**
     * @param Params $params
     * @return PaginatedArrayCollection
     */
    public function findPageBy(Params $params): PaginatedArrayCollection
    {
        $qb = $this->createPaginatedQueryBuilder($params->getCriteria(), null, $params->getOrderBy());

        $qb->addSelect($this->getEntityAlias());
        $this->processOrderBy($qb, $params->getOrderBy());

        // find all
        if ($params->getPerPage() > 0) {
            $qb->addPagination($params->getPage(), $params->getPerPage());
        }
        $results = (is_null($params->getResultSql())) ? $qb->getQuery()->getResult($params->getHydrateMode()) : $params->getResultSql();

        if (!is_null($params->getCountQtdSql())) {
            $total = $params->getCountQtdSql();
        } else {
            // count elements if needed
            $total = -1;
            if ($params->getPerPage() > 0) {
                $total = count($results) < $params->getPerPage() && $params->getPage() == 1 ? count($results) : $this->countBy($params->getCriteria());
            }
        }

        return new PaginatedArrayCollection($results, $params->getPage(), $params->getPerPage(), $total, $params->getCriteria(), $params->getOrderBy());
    }

    /**
     * @param array|null $criteria
     * @return int
     */
    public function countBy(?array $criteria = []): int
    {
        try {
            $qb = $this->createPaginatedQueryBuilder($criteria);
            $qb->select('COUNT(' . $this->getEntityAlias() . ')');

            return (int)$qb->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException | NoResultException $e) {
            return 0;
        }
    }

    /**
     * @param array       $criteria
     * @param string|null $indexBy
     * @param array|null  $orderBy
     * @return PaginatedQueryBuilder
     */
    protected function createPaginatedQueryBuilder(array $criteria = [], ?string $indexBy = null, ?array $orderBy = null): PaginatedQueryBuilder
    {
        $qb = new PaginatedQueryBuilder($this->_em);
        $qb->from($this->_entityName, $this->getEntityAlias(), $indexBy);

        if (!empty($orderBy)) {
            $qb->addOrder($orderBy, $this->getEntityAlias());
        }

        $this->processCriteria($qb, $criteria);
        return $qb;
    }

    protected function processCriteria(PaginatedQueryBuilder $qb, array $criteria): void
    {
        if ($this instanceof FilterRepositoryInterface) {
            $this->buildFilterCriteria($qb, $criteria);
        } else {
            foreach ($criteria as $field => $value) {
                $fieldParameter = 'f' . substr(md5($field), 0, 5);

                if (is_null($value)) {
                    $qb->andWhere(sprintf('%s.%s IS NULL', $this->getEntityAlias(), $field));
                } elseif (is_array($value) && in_array(strtoupper($value[0]), ["LIKE", "ILIKE"])) {
                    $qb->andWhere(
                        $qb->expr()->like(
                            sprintf('LOWER(%s.%s)', $this->getEntityAlias(), $field),
                            $qb->expr()->literal(strtolower($value[1]) . '%')
                        )
                    );
                } elseif (is_array($value)) {
                    $qb->andWhere($qb->expr()->in(sprintf('%s.%s', $this->getEntityAlias(), $field), $value));
                } else {
                    $qb->andWhere(sprintf('%s.%s = :%s', $this->getEntityAlias(), $field, $fieldParameter));
                    $qb->setParameter($fieldParameter, $value);
                }
            }
        }
    }

    /**
     * @param PaginatedQueryBuilder $qb
     * @param array|null $orderBy
     */
    protected function processOrderBy(PaginatedQueryBuilder $qb, ?array $orderBy = null): void
    {
        if (is_array($orderBy)) {
            $qb->addOrder($orderBy, $this->getEntityAlias());
        }
    }

    /**
     * @return string
     */
    protected function getEntityAlias(): string
    {
        $entityName = explode('\\', $this->_entityName);

        return strtolower(substr(array_pop($entityName), 0, 1));
    }
}
