<?php

namespace Repository\Base\Fetchers;

use Data\Provider\Interfaces\DataProviderInterface;
use Data\Provider\Interfaces\QueryCriteriaInterface;
use Exception;
use Repository\Base\Interfaces\FetcherInterface;
use Repository\Base\Interfaces\ModelFactoryInterface;

class DataProviderFetcherBuilder
{
    private DataProviderInterface $dataProvider;
    private bool $isMultipleValue = false;
    private string $fillingKeyName = '';
    private string $foreignKeyName = '';
    private string $destinationKeyName = '';
    private ?ModelFactoryInterface $modelFactory = null;
    private ?QueryCriteriaInterface $query = null;
    private ?string $compareKeyName = null;
    /**
     * @var callable|null
     */
    private $compareCallback = null;
    /**
     * @var callable|null
     */
    private $itemFillCallback = null;

    public static function init(
        DataProviderInterface $dataProvider,
        bool $isMultipleValue = false
    ): DataProviderFetcherBuilder {
        return new DataProviderFetcherBuilder($dataProvider, $isMultipleValue);
    }

    public function __construct(DataProviderInterface $dataProvider, bool $isMultipleValue = false)
    {
        $this->dataProvider = $dataProvider;
        $this->isMultipleValue = $isMultipleValue;
    }

    public function setFillingKeyName(string $name): DataProviderFetcherBuilder
    {
        $this->fillingKeyName = $name;
        return $this;
    }

    public function setForeignKeyName(string $name): DataProviderFetcherBuilder
    {
        $this->foreignKeyName = $name;
        return $this;
    }

    public function setDestinationKeyName(string $name): DataProviderFetcherBuilder
    {
        $this->destinationKeyName = $name;
        return $this;
    }

    public function setQuery(QueryCriteriaInterface $query): DataProviderFetcherBuilder
    {
        $this->query = $query;
        return $this;
    }

    public function setModelFactory(ModelFactoryInterface $factory): DataProviderFetcherBuilder
    {
        $this->modelFactory = $factory;
        return $this;
    }

    public function setCompareKeyName(string $name): DataProviderFetcherBuilder
    {
        $this->compareKeyName = $name;
        return $this;
    }

    public function setCompareCallback(callable $callback): DataProviderFetcherBuilder
    {
        $this->compareCallback = $callback;
        return $this;
    }

    public function setItemFillCallback(callable $callback): DataProviderFetcherBuilder
    {
        $this->itemFillCallback = $callback;
        return $this;
    }

    /**
     * @throws Exception
     */
    public function build(): FetcherInterface
    {
        return new DataProviderFetcher(
            $this->dataProvider,
            $this->fillingKeyName,
            $this->foreignKeyName,
            $this->destinationKeyName,
            $this->isMultipleValue,
            $this->query,
            $this->modelFactory,
            $this->compareKeyName,
            $this->compareCallback,
            $this->itemFillCallback
        );
    }
}
