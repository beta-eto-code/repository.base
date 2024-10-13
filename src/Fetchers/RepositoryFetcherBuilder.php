<?php

namespace Repository\Base\Fetchers;

use Data\Provider\Interfaces\QueryCriteriaInterface;
use Exception;
use Repository\Base\Interfaces\FetcherInterface;
use Repository\Base\Interfaces\ModelFactoryInterface;
use Repository\Base\Interfaces\ReadableRepositoryInterface;

class RepositoryFetcherBuilder
{
    private ReadableRepositoryInterface $repository;
    private bool $isMultipleValue = false;
    private string $fillingKeyName = '';
    private string $foreignKeyName = '';
    private string $destinationKeyName = '';
    private ?ModelFactoryInterface $modelFactory = null;
    private ?QueryCriteriaInterface $query = null;
    /**
     * @var callable|null
     */
    private $compareCallback = null;
    /**
     * @var callable|null
     */
    private $itemFillCallback = null;

    public static function init(
        ReadableRepositoryInterface $repository,
        bool $isMultipleValue = false
    ): RepositoryFetcherBuilder {
        return new RepositoryFetcherBuilder($repository, $isMultipleValue);
    }

    public function __construct(ReadableRepositoryInterface $repository, bool $isMultipleValue = false)
    {
        $this->repository = $repository;
        $this->isMultipleValue = $isMultipleValue;
    }

    public function setFillingKeyName(string $name): RepositoryFetcherBuilder
    {
        $this->fillingKeyName = $name;
        return $this;
    }

    public function setForeignKeyName(string $name): RepositoryFetcherBuilder
    {
        $this->foreignKeyName = $name;
        return $this;
    }

    public function setDestinationKeyName(string $name): RepositoryFetcherBuilder
    {
        $this->destinationKeyName = $name;
        return $this;
    }

    public function setQuery(QueryCriteriaInterface $query): RepositoryFetcherBuilder
    {
        $this->query = $query;
        return $this;
    }

    public function setModelFactory(ModelFactoryInterface $factory): RepositoryFetcherBuilder
    {
        $this->modelFactory = $factory;
        return $this;
    }

    public function setCompareCallback(callable $callback): RepositoryFetcherBuilder
    {
        $this->compareCallback = $callback;
        return $this;
    }

    public function setItemFillCallback(callable $callback): RepositoryFetcherBuilder
    {
        $this->itemFillCallback = $callback;
        return $this;
    }

    /**
     * @throws Exception
     */
    public function build(): FetcherInterface
    {
        return new RepositoryFetcher(
            $this->repository,
            $this->fillingKeyName,
            $this->foreignKeyName,
            $this->destinationKeyName,
            $this->isMultipleValue,
            $this->query,
            $this->modelFactory,
            $this->compareCallback,
            $this->itemFillCallback
        );
    }
}
