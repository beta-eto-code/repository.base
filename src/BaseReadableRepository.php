<?php

namespace Repository\Base;

use Collection\Base\Collection;
use Collection\Base\Interfaces\CollectionInterface;
use Collection\Base\Interfaces\CollectionItemInterface;
use Collection\Base\Storage\IteratorStorage;
use Data\Provider\Interfaces\DataProviderInterface;
use Data\Provider\Interfaces\QueryCriteriaInterface;
use EmptyIterator;
use Exception;
use Iterator;
use Model\Base\Interfaces\ModelInterface;
use Repository\Base\Interfaces\AccessRecipientContextInterface;
use Repository\Base\Interfaces\FetcherInterface;
use Repository\Base\Interfaces\ModelFactoryInterface;
use Repository\Base\Interfaces\PkCriteriaInterface;
use Repository\Base\Interfaces\ReadableRepositoryInterface;

abstract class BaseReadableRepository implements ReadableRepositoryInterface
{
    protected DataProviderInterface $dataProvider;
    /**
     * @var FetcherInterface[]|null
     */
    private ?array $fetcherList = null;

    /**
     * @throws Exception
     */
    public function __construct(DataProviderInterface $dataProvider)
    {
        $classModel = $this->getClassModel();
        if (!is_a($classModel, ModelInterface::class, true)) {
            throw new Exception('Invalid model class - ' . $classModel);
        }
        $this->dataProvider = $dataProvider;
    }

    /**
     * @return string|ModelInterface
     */
    abstract protected function getClassModel(): string;

    /**
     * @return array<string, FetcherInterface>
     */
    abstract protected function getFetcherList(): array;

    protected function getCachedFetcherList(): array
    {
        if (is_null($this->fetcherList)) {
            $this->fetcherList = $this->getFetcherList();
        }

        return $this->fetcherList;
    }

    public function getCount(
        QueryCriteriaInterface $query,
        ?AccessRecipientContextInterface $recipientContext = null
    ): int {
        return $this->dataProvider->getDataCount($query);
    }

    public function getCollection(
        QueryCriteriaInterface $query,
        ?AccessRecipientContextInterface $recipientContext = null,
        string ...$fetchListNames
    ): CollectionInterface {
        $collection = new Collection([], new IteratorStorage($this->getIterator($query, $recipientContext)));
        if (empty($fetchListNames)) {
            return $collection;
        }

        foreach ($this->getFetcherListByNames(...$fetchListNames) as $fetcher) {
            $fetcher->fill($collection);
        }
        return $collection;
    }

    public function getIterator(
        QueryCriteriaInterface $query,
        ?AccessRecipientContextInterface $recipientContext = null
    ): Iterator {
        foreach ($this->dataProvider->getIterator($query) as $data) {
            yield $this->createModelFromArrayData($data);
        }
        return new EmptyIterator();
    }

    public function getByPk(
        PkCriteriaInterface $pk,
        ?AccessRecipientContextInterface $recipientContext = null
    ): ?CollectionItemInterface {
        $query = $pk->createQuery();
        $query->setLimit(1);
        $data = current($this->dataProvider->getData($query));
        return empty($data) ? null : $this->createModelFromArrayData($data);
    }

    private function createModelFromArrayData(array $data): ModelInterface
    {
        $classModel = $this->getClassModel();
        return $classModel::initFromArray($data);
    }

    public function getModelCollection(
        ModelFactoryInterface $modelFactory,
        QueryCriteriaInterface $query,
        ?AccessRecipientContextInterface $recipientContext = null,
        FetcherInterface ...$fetcherList
    ): CollectionInterface {
        $collection = new Collection(
            [],
            new IteratorStorage($this->getModelIterator($modelFactory, $query, $recipientContext))
        );

        if (empty($fetcherList)) {
            return $collection;
        }

        foreach ($fetcherList as $fetcher) {
            $fetcher->fill($collection);
        }
        return $collection;
    }

    public function getFetcherListByNames(string ...$fetchListNames): array
    {
        $result = [];
        foreach ($this->getCachedFetcherList() as $name => $fetcher) {
            if (in_array($name, $fetchListNames, true)) {
                $result[$name] = $fetcher;
            }
        }
        return $result;
    }

    public function getModelIterator(
        ModelFactoryInterface $modelFactory,
        QueryCriteriaInterface $query,
        ?AccessRecipientContextInterface $recipientContext = null
    ): Iterator {
        foreach ($this->dataProvider->getIterator($query) as $data) {
            yield $modelFactory->createModelFromArray($data);
        }
        return new EmptyIterator();
    }
}
