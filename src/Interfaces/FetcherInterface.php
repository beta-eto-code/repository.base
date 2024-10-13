<?php

namespace Repository\Base\Interfaces;

use Collection\Base\Interfaces\CollectionInterface;

interface FetcherInterface
{
    public function fill(CollectionInterface $collection): void;
}
