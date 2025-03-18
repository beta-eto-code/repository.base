<?php

namespace Repository\Base\Interfaces;

use Access\Scope\Interfaces\AccessRecipientContextInterface;
use Collection\Base\Interfaces\CollectionInterface;

interface FetcherInterface
{
    public function fill(
        CollectionInterface $collection,
        ?AccessRecipientContextInterface $recipientContext = null
    ): void;
}
