<?php

declare(strict_types=1);

namespace Pagination\DTO;

use LogicException;

class PaginatedArrayCollection
{
    /**
     * @var int|null
     */
    protected $total, $lastpage, $perpage, $currentpage;

    /**
     * @var string|null
     */
    protected $nextpageurl, $prevpageurl;

    /**
     * @var array|null
     */
    protected $criteria = [], $orderby = [], $data = null;

    public function __construct(
        array $elements = [],
        int $currentpage = null,
        int $perpage = 10,
        int $total = null,
        ?array $criteria = [],
        ?array $orderby = []
    ) {
        $this->data = $elements;
        $this->total = $total;
        $this->perpage = $perpage;
        $this->currentpage = $currentpage;
        $this->criteria = $criteria;
        $this->orderby = $orderby;

        $this->lastpage = $this->getLastPage();
        $this->nextpageurl = $this->getNextPageUrl();
        $this->prevpageurl = $this->getPrevPageUrl();

        $this->criteria = null;
        $this->orderby = null;
    }

    /**
     * @param int|null $total
     * @return PaginatedArrayCollection
     */
    public function setTotal(?int $total): PaginatedArrayCollection
    {
        $this->total = $total;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getTotal(): ?int
    {
        return $this->total;
    }

    /**
     * @param int|null $lastpage
     * @return PaginatedArrayCollection
     */
    public function setLastPage(?int $lastpage): PaginatedArrayCollection
    {
        $this->lastpage = $lastpage;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getLastPage(): ?int
    {
        if (!$this->getPerPage()) {
            throw new LogicException('ResultsPerPage was not setted');
        }

        if (!$this->getTotal()) {
            return 0;
        }
        $this->lastpage = ceil($this->getTotal() / $this->getPerPage());

        return intval($this->lastpage);
    }

    /**
     * @param int|null $perpage
     * @return PaginatedArrayCollection
     */
    public function setPerPage(?int $perpage): PaginatedArrayCollection
    {
        $this->perpage = $perpage;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getPerPage(): ?int
    {
        return $this->perpage;
    }

    /**
     * @param int|null $currentpage
     * @return PaginatedArrayCollection
     */
    public function setCurrentPage(?int $currentpage): PaginatedArrayCollection
    {
        $this->currentpage = $currentpage;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getCurrentPage(): ?int
    {
        return $this->currentpage;
    }

    /**
     * @param string|null $nextpageurl
     * @return PaginatedArrayCollection
     */
    public function setNextpageUrl(?string $nextpageurl): PaginatedArrayCollection
    {
        $this->nextpageurl = $nextpageurl;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getNextPageUrl(): ?string
    {
        $this->nextpageurl = ($this->getCurrentPage() === $this->getLastPage()) ? null : $this->mountUrl($this->getCurrentPage() + 1);
        return $this->nextpageurl;
    }

    /**
     * @param string|null $prevpageurl
     * @return PaginatedArrayCollection
     */
    public function setPrevpageUrl(?string $prevpageurl): PaginatedArrayCollection
    {
        $this->prevpageurl = $prevpageurl;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPrevPageUrl(): ?string
    {
        $this->prevpageurl = ($this->getCurrentPage() === 1) ? null : $this->mountUrl($this->getCurrentPage() - 1);
        return $this->prevpageurl;
    }

    /**
     * @return array|null
     */
    public function getCriteria(): ?array
    {
        return $this->criteria;
    }

    /**
     * @param array|null $criteria
     * @return PaginatedArrayCollection
     */
    public function setCriteria(?array $criteria): PaginatedArrayCollection
    {
        $this->criteria = $criteria;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getOrderBy(): ?array
    {
        return $this->orderby;
    }

    /**
     * @param array|null $orderby
     * @return PaginatedArrayCollection
     */
    public function setOrderBy(?array $orderby): PaginatedArrayCollection
    {
        $this->orderby = $orderby;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getData(): ?array
    {
        return $this->data;
    }

    /**
     * @param array|null $data
     * @return PaginatedArrayCollection
     */
    public function setData(?array $data): PaginatedArrayCollection
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @param int $page
     * @return string
     */
    private function mountUrl(int $page): string
    {
        $order = '';
        $criteria = '';

        if ($page < 1) {
            $page = 1;
        }

        if ($page > $this->getTotal()) {
            $page = $this->getTotal();
        }

        if (!empty($this->criteria)) {
            foreach ($this->criteria as $key => $data) {
                if (!is_array($data)) {
                    $param = sprintf("&%s=%s", $key, $data);
                } else {
                    $param = sprintf("&search=%s&searchfield=%s", $data[1] ?? $data, $key);
                }

                $criteria .= $param;
            }
        }

        if (!empty($this->orderby)) {
            foreach ($this->orderby as $key => $data) {
                $order .= sprintf("&sort=%s&order=%s", $key, $data);
            }
        }
        return sprintf("?page=%s&limit=%s%s%s", $page, $this->getPerPage(), $order, $criteria);
    }

    /**
     * @return PaginatedArrayCollection
     */
    public function resultPagination(): PaginatedArrayCollection
    {
        $this->data = null;
        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}
