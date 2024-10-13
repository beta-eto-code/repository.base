<?php

namespace Repository\Base\Tests\Stubs;

use Data\Provider\Interfaces\DataProviderInterface;
use Data\Provider\Providers\ArrayDataProvider;
use Exception;
use Repository\Base\BaseReadableRepository;
use Repository\Base\Fetchers\DataProviderFetcherBuilder;
use Repository\Base\Fetchers\RepositoryFetcherBuilder;
use Repository\Base\Interfaces\FetcherInterface;

class UserWithPhotoRepository extends BaseReadableRepository
{
    private DataProviderInterface $photoDataProvider;
    private PhotoRepository $photoRepository;

    public function __construct(
        DataProviderInterface $dataProvider,
        ?DataProviderInterface $photoDataProvider = null
    ) {
        parent::__construct($dataProvider);
        $this->photoDataProvider = $photoDataProvider ?? new ArrayDataProvider([]);
        $this->photoRepository = new PhotoRepository($this->photoDataProvider);
    }

    protected function getClassModel(): string
    {
        return UserWithPhotoModel::class;
    }

    /**
     * @throws Exception
     */
    protected function getFetcherList(): array
    {
        return [
            'photoData' => $this->createPhotoDataFetcher(),
            'photoModel' => $this->createPhotoRepositoryFetcher()
        ];
    }

    /**
     * @throws Exception
     */
    private function createPhotoDataFetcher(): FetcherInterface
    {
        return DataProviderFetcherBuilder::init($this->photoDataProvider)
            ->setFillingKeyName('photoData')
            ->setForeignKeyName('photoId')
            ->setDestinationKeyName('id')
            ->build();
    }

    /**
     * @throws Exception
     */
    private function createPhotoRepositoryFetcher(): FetcherInterface
    {
        return RepositoryFetcherBuilder::init($this->photoRepository)
            ->setFillingKeyName('photoModel')
            ->setForeignKeyName('photoId')
            ->setDestinationKeyName('id')
            ->build();
    }
}
