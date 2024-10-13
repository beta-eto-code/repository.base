<?php

namespace Repository\Base\Filters;

use Data\Provider\Interfaces\CompareRuleInterface;
use Data\Provider\Interfaces\QueryCriteriaInterface;
use Data\Provider\QueryCriteria;
use Repository\Base\Interfaces\PkCriteriaInterface;

class SimplePk implements PkCriteriaInterface
{
    private mixed $pkValue;
    private string $fieldName;

    public function __construct(mixed $pkValue, string $fieldName = 'id')
    {
        $this->pkValue = $pkValue;
        $this->fieldName = $fieldName;
    }

    public function createQuery(): QueryCriteriaInterface
    {
        $query = new QueryCriteria();
        $query->addCriteria($this->fieldName, CompareRuleInterface::EQUAL, $this->pkValue);
        return $query;
    }
}
