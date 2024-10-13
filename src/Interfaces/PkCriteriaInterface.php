<?php

namespace Repository\Base\Interfaces;

use Data\Provider\Interfaces\QueryCriteriaInterface;

interface PkCriteriaInterface
{
    public function createQuery(): QueryCriteriaInterface;
}
