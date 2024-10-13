<?php

namespace Repository\Base\Filters;

use Data\Provider\Interfaces\CompareRuleInterface;
use Data\Provider\Interfaces\QueryCriteriaInterface;
use Data\Provider\QueryCriteria;
use Exception;
use Repository\Base\Interfaces\PkCriteriaInterface;

class ComplexPk implements PkCriteriaInterface
{
    private array $complexFilter;

    public function __construct(array $complexFilter)
    {
        $this->complexFilter = $complexFilter;
    }

    /**
     * @throws Exception
     */
    public function createQuery(): QueryCriteriaInterface
    {
        if (empty($this->complexFilter)) {
            throw new Exception('Complex filter is empty');
        }

        $query = new QueryCriteria();
        foreach ($this->complexFilter as $key => $value) {
            $query->addCriteria($key, CompareRuleInterface::EQUAL, $value);
        }
        return $query;
    }
}
