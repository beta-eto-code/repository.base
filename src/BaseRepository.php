<?php

namespace Repository\Base;

use Collection\Base\Interfaces\CollectionInterface;
use Data\Provider\Interfaces\OperationResultInterface;
use Data\Provider\Interfaces\PkOperationResultInterface;
use Data\Provider\Interfaces\QueryCriteriaInterface;
use Data\Provider\OperationResult;
use Repository\Base\Interfaces\AccessRecipientContextInterface;
use Repository\Base\Interfaces\PkCriteriaInterface;
use Repository\Base\Interfaces\RemovableRepositoryInterface;
use Repository\Base\Interfaces\SavableRepositoryInterface;

abstract class BaseRepository extends BaseReadableRepository implements
    SavableRepositoryInterface, RemovableRepositoryInterface
{
    public function saveCollection(
        CollectionInterface $collection,
        ?AccessRecipientContextInterface $recipientContext = null
    ): PkOperationResultInterface {
        $result = new OperationResult();
        foreach ($collection as $item) {
            $result->addNext($this->save($item, $recipientContext));
        }
        return $result;
    }

    public function deleteByPk(
        PkCriteriaInterface $pk,
        ?AccessRecipientContextInterface $recipientContext = null
    ): OperationResultInterface {
        return $this->delete($pk->createQuery(), $recipientContext);
    }

    public function delete(
        QueryCriteriaInterface $query,
        ?AccessRecipientContextInterface $recipientContext = null
    ): OperationResultInterface {
        return $this->dataProvider->remove($query);
    }
}
