<?php

namespace Repository\Base\Fetchers;

use Access\Scope\Interfaces\AccessRecipientContextInterface;
use Collection\Base\Interfaces\CollectionInterface;
use Collection\Base\Interfaces\CollectionItemInterface;
use Data\Provider\Interfaces\CompareRuleInterface;
use Data\Provider\Interfaces\QueryCriteriaInterface;
use Data\Provider\QueryCriteria;
use EmptyIterator;
use Exception;
use Iterator;
use Repository\Base\Interfaces\FetcherInterface;
use Repository\Base\Interfaces\ModelFactoryInterface;
use Repository\Base\Interfaces\ReadableRepositoryInterface;

class RepositoryFetcher implements FetcherInterface
{
    private ReadableRepositoryInterface $repository;
    private string $fillingKeyName;
    private string $foreignKey;
    private string $destinationKey;
    private bool $isMultipleValue;
    private ?QueryCriteriaInterface $query;
    private ?ModelFactoryInterface $modelFactory;
    /**
     * @var callable|null
     */
    private $compareCallback;
    /**
     * @var callable|null
     */
    private $itemFillCallback;

    private array $fetchListNames = [];
    private ?string $compareKeyName = null;

    /**
     * @throws Exception
     */
    public function __construct(
        ReadableRepositoryInterface $repository,
        string $fillingKeyName = '',
        string $foreignKey = '',
        string $destinationKey = '',
        bool $isMultipleValue = false,
        ?QueryCriteriaInterface $query = null,
        ?ModelFactoryInterface $modelFactory = null,
        ?string $compareKeyName = null,
        ?callable $compareCallback = null,
        ?callable $itemFillCallback = null,
        array $fetchListNames = []
    ) {
        $emptyCompareCallback = empty($compareCallback);
        if ($emptyCompareCallback && empty($foreignKey)) {
            throw new Exception('Не указан внешний ключ для связи');
        }

        if ($emptyCompareCallback && empty($destinationKey)) {
            throw new Exception('Не указан удаленный ключ для связи');
        }

        $this->repository = $repository;
        $this->fillingKeyName = $fillingKeyName;
        $this->foreignKey = $foreignKey;
        $this->destinationKey = $destinationKey;
        $this->isMultipleValue = $isMultipleValue;
        $this->query = $query;
        $this->modelFactory = $modelFactory;
        $this->compareKeyName = $compareKeyName;
        $this->compareCallback = $compareCallback;
        $this->itemFillCallback = $itemFillCallback;
        $this->fetchListNames = $fetchListNames;
    }

    public function fill(
        CollectionInterface $collection,
        ?AccessRecipientContextInterface $recipientContext = null
    ): void {
        if ($collection->count() === 0) {
            return;
        }

        $query = $this->createQuery($collection);
        $destinationCollection = $this->getRepositoryCollection($query, $recipientContext);
        if (empty($this->compareCallback)) {
            $this->fillByMappedList($collection, $destinationCollection);
            return;
        }

        foreach ($destinationCollection as $destinationItem) {
            foreach ($collection as $originItem) {
                if ($this->isItemsLinked($originItem, $destinationItem)) {
                    $this->fillItem($originItem, $destinationItem);
                }
            }
        }
    }

    private function fillByMappedList(CollectionInterface $collection, CollectionInterface $destinationCollection): void
    {
        $mappedList = $this->getMappedList($destinationCollection);
        foreach ($collection as $item) {
            $originKey = $item->getValueByKey($this->foreignKey);
            $keyList = is_array($originKey) ? $originKey : [$originKey];
            foreach ($keyList as $key) {
                $destinationItem = $mappedList[$key] ?? null;
                if (is_null($destinationItem)) {
                    continue;
                }

                $destinationItem = is_array($destinationItem) ? $destinationItem : [$destinationItem];
                foreach ($destinationItem as $value) {
                    $this->fillItem($item, $value);
                }
            }
        }
    }

    private function getMappedList(CollectionInterface $collection): array
    {
        $resultList = [];
        $compareKeyName = $this->compareKeyName ?? $this->destinationKey;
        foreach ($collection as $originItem) {
            $originalValue = $originItem->getValueByKey($compareKeyName);
            $value = is_iterable($originalValue) ? $originalValue : [$originalValue];
            foreach ($value as $key) {
                if ($this->isMultipleValue) {
                    $resultList[$key][] = $originItem;
                } else {
                    $resultList[$key] = $originItem;
                }
            }
        }

        return $resultList;
    }

    private function createQuery(CollectionInterface $collection): QueryCriteriaInterface
    {
        $query = $this->query ?? new QueryCriteria();
        $foreignKeyValues = iterator_to_array($this->getForeignKeyValueIterator($collection));
        if (!empty($this->destinationKey)) {
            $query->addCriteria($this->destinationKey, CompareRuleInterface::IN, $foreignKeyValues);
        }
        return $query;
    }

    private function getForeignKeyValueIterator(CollectionInterface $collection): Iterator
    {
        if (empty($this->foreignKey)) {
            return new EmptyIterator();
        }

        foreach ($collection as $item) {
            foreach ((array) $item->getValueByKey($this->foreignKey) as $value) {
                if (!empty($value) && is_scalar($value)) {
                    yield $value;
                }
            }
        }
        return new EmptyIterator();
    }

    private function getRepositoryCollection(
        QueryCriteriaInterface $query,
        ?AccessRecipientContextInterface $recipientContext = null
    ): CollectionInterface {
        return $this->modelFactory ?
            $this->repository->getModelCollection(
                $this->modelFactory,
                $query,
                $recipientContext,
                ...$this->repository->getFetcherListByNames(...$this->fetchListNames)
            ) :
            $this->repository->getCollection($query, $recipientContext, ...$this->fetchListNames);
    }

    private function isItemsLinked(CollectionItemInterface $originItem, CollectionItemInterface $destinationItem): bool
    {
        if (!empty($this->compareCallback) && ($this->compareCallback)($originItem, $destinationItem)) {
            return true;
        }

        if (empty($this->foreignKey)) {
            return false;
        }

        $originValues = (array) $originItem->getValueByKey($this->foreignKey);
        $destinationValue = $destinationItem->getValueByKey($this->destinationKey);
        return !empty($originValues) && !empty($destinationValue) && in_array($destinationValue, $originValues);
    }

    private function fillItem(CollectionItemInterface $originItem, CollectionItemInterface $destinationItem): void
    {
        if (!empty($this->itemFillCallback)) {
            ($this->itemFillCallback)($originItem, $destinationItem);
            return;
        }

        if (!$this->isMultipleValue) {
            $originItem->setValueByKey($this->fillingKeyName, $destinationItem);
            return;
        }

        $currentValue = $originItem->getValueByKey($this->fillingKeyName);
        if ($currentValue instanceof CollectionInterface) {
            $currentValue->append($destinationItem);
            return;
        }

        if (is_array($currentValue)) {
            $currentValue[] = $destinationItem;
            $originItem->setValueByKey($this->fillingKeyName, $currentValue);
        }
    }
}
