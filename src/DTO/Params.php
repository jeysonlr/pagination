<?php

declare(strict_types=1);

namespace Pagination\DTO;

use Doctrine\ORM\AbstractQuery;

class Params
{
    const NUMBER_ONE = 1, NUMBER_TWENTY = 20, SORT = 'ASC';

    /**
     * @var int|null
     */
    private $page = self::NUMBER_ONE,
        $perPage = self::NUMBER_TWENTY,
        $hydrateMode = AbstractQuery::HYDRATE_OBJECT,
        $countQtdSql;

    /**
     * @var string|null
     */
    private $sort = self::SORT,
        $order = '',
        $search = '',
        $searchField = '';

    /**
     * @var array|null
     */
    private $criteria = [], $resultSql;

    public function __construct(?array $dados = [])
    {
        if (empty($dados))
            return;

        foreach ($dados as $key => $dado) {
            $key = trim($key);
            $dado = trim($dado);

            if (!isset($this->$key) || $dado === "undefined") {
                continue;
            }

            $this->$key = $this->treatData($key, $dado);
        }
    }

    /**
     * @return int|null
     */
    public function getPage(): ?int
    {
        return $this->page;
    }

    /**
     * @param int|null $page
     * @return Params
     */
    public function setPage(?int $page): Params
    {
        $this->page = $page;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getPerPage(): ?int
    {
        return $this->perPage;
    }

    /**
     * @param int|null $perPage
     * @return Params
     */
    public function setPerPage(?int $perPage): Params
    {
        $this->perPage = $perPage;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getCriteria(): ?array
    {
        if (empty($this->getSearch()) || empty($this->getSearchField())) {
            return $this->criteria;
        }

        return array_merge($this->criteria, [
            $this->getSearchField() => ["ILIKE", $this->getSearch()]
        ]);
    }

    /**
     * @param array|null $criteria
     * @return Params
     */
    public function setCriteria(?array $criteria): Params
    {
        $this->criteria = $criteria;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSort(): ?string
    {
        return $this->sort;
    }

    /**
     * @param string|null $sort
     * @return Params
     */
    public function setSort(?string $sort): Params
    {
        $this->sort = $sort;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getOrder(): ?string
    {
        return $this->order;
    }

    /**
     * @param string|null $order
     * @return Params
     */
    public function setOrder(?string $order): Params
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @return array|string|null
     */
    public function getOrderBy(): ?array
    {
        if ($this->getSort() && $this->getOrder()) {
            return [$this->getOrder() => $this->getSort()];
        }

        return [];
    }

    /**
     * @return string|null
     */
    public function getSearch(): ?string
    {
        return $this->search;
    }

    /**
     * @param string|null $search
     * @return Params
     */
    public function setSearch(?string $search): Params
    {
        $this->search = $search;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSearchField(): ?string
    {
        return $this->searchField;
    }

    /**
     * @param string|null $searchField
     * @return Params
     */
    public function setSearchField(?string $searchField): Params
    {
        $this->searchField = $searchField;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getHydrateMode(): ?int
    {
        return $this->hydrateMode;
    }

    /**
     * @param int|null $hydrateMode
     * @return Params
     */
    public function setHydrateMode(?int $hydrateMode): Params
    {
        $this->hydrateMode = $hydrateMode;
        return $this;
    }

    /**
     * @param $key
     * @param $data
     * @return array|int|string|null
     */
    private function treatData($key, $data): ?array
    {
        $typeData = gettype($this->$key);

        switch ($typeData) {
            case "integer":
            case "string":
                $method = sprintf("%sval", substr($typeData, 0, 3));

                return call_user_func($method, $data);
            case "array":
                return is_array($data) ? $data : (array)$data;
            default:
                return $data;
        }
    }

    /**
     * @return int|null
     */
    public function getCountQtdSql(): ?int
    {
        return $this->countQtdSql;
    }

    /**
     * @param int|null $countQtdSql
     * @return Params
     */
    public function setCountQtdSql(?int $countQtdSql): Params
    {
        $this->countQtdSql = $countQtdSql;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getResultSql(): ?array
    {
        return $this->resultSql;
    }

    /**
     * @param array|null $resultSql
     * @return Params
     */
    public function setResultSql(?array $resultSql): Params
    {
        $this->resultSql = $resultSql;
        return $this;
    }
}
