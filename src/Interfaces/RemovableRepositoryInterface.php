<?php

namespace Repository\Base\Interfaces;


use Data\Provider\Interfaces\OperationResultInterface;
use Data\Provider\Interfaces\QueryCriteriaInterface;

interface RemovableRepositoryInterface
{
    public function delete(
        QueryCriteriaInterface $query,
        ?AccessRecipientContextInterface $recipientContext = null
    ): OperationResultInterface;

    public function deleteByPk(
        PkCriteriaInterface $pk,
        ?AccessRecipientContextInterface $recipientContext = null
    ): OperationResultInterface;
}
