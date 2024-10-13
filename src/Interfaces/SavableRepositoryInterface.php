<?php

namespace Repository\Base\Interfaces;


use Collection\Base\Interfaces\CollectionInterface;
use Collection\Base\Interfaces\CollectionItemInterface;
use Data\Provider\Interfaces\PkOperationResultInterface;

interface SavableRepositoryInterface
{
    public function saveCollection(
        CollectionInterface $collection,
        ?AccessRecipientContextInterface $recipientContext = null
    ): PkOperationResultInterface;

    public function save(
        CollectionItemInterface $item,
        ?AccessRecipientContextInterface $recipientContext = null
    ): PkOperationResultInterface;
}
