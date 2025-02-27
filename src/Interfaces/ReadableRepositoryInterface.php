<?php

namespace Repository\Base\Interfaces;

use Access\Scope\Interfaces\AccessRecipientContextInterface;
use Collection\Base\Interfaces\CollectionInterface;
use Collection\Base\Interfaces\CollectionItemInterface;
use Data\Provider\Interfaces\QueryCriteriaInterface;
use Iterator;

interface ReadableRepositoryInterface
{
    public function getCount(
        QueryCriteriaInterface $query,
        ?AccessRecipientContextInterface $recipientContext = null
    ): int;

    public function getCollection(
        QueryCriteriaInterface $query,
        ?AccessRecipientContextInterface $recipientContext = null,
        string ...$fetchListNames
    ): CollectionInterface;

    /**
     * @param QueryCriteriaInterface $query
     * @param AccessRecipientContextInterface|null $recipientContext
     * @return Iterator|CollectionItemInterface[]
     */
    public function getIterator(
        QueryCriteriaInterface $query,
        ?AccessRecipientContextInterface $recipientContext = null
    ): Iterator;

    public function getByPk(
        PkCriteriaInterface $pk,
        ?AccessRecipientContextInterface $recipientContext = null
    ): ?CollectionItemInterface;

    public function getModelCollection(
        ModelFactoryInterface $modelFactory,
        QueryCriteriaInterface $query,
        ?AccessRecipientContextInterface $recipientContext = null,
        FetcherInterface ...$fetcherList
    ): CollectionInterface;

    /**
     * @param ModelFactoryInterface $modelFactory
     * @param QueryCriteriaInterface $query
     * @param AccessRecipientContextInterface|null $recipientContext
     * @return Iterator|CollectionItemInterface[]
     */
    public function getModelIterator(
        ModelFactoryInterface $modelFactory,
        QueryCriteriaInterface $query,
        ?AccessRecipientContextInterface $recipientContext = null
    ): Iterator;
}
