<?php

namespace Repository\Base\Fetchers\Tests;

use Collection\Base\Interfaces\CollectionInterface;
use Data\Provider\Interfaces\CompareRuleInterface;
use Data\Provider\Providers\ArrayDataProvider;
use Data\Provider\QueryCriteria;
use Exception;
use PHPUnit\Framework\TestCase;
use Repository\Base\Fetchers\DataProviderFetcherBuilder;
use Repository\Base\Fetchers\RepositoryFetcherBuilder;
use Repository\Base\Tests\Stubs\DocModel;
use Repository\Base\Tests\Stubs\DocRepository;
use Repository\Base\Tests\Stubs\PhotoModel;
use Repository\Base\Tests\Stubs\PhotoRepository;
use Repository\Base\Tests\Stubs\ProfileRepository;
use Repository\Base\Tests\Stubs\UserWithPhotoModel;
use Repository\Base\Tests\Stubs\UserWithPhotoRepository;

class RepositoryFetcherTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testFillSingleValue()
    {
        $userWithPhotoDataProvider = new ArrayDataProvider([
            ['id' => '1', 'name' => 'John', 'email' => 'john@example.com', 'photoId' => 1],
            ['id' => '2', 'name' => 'Jane', 'email' => 'jane@example.com', 'photoId' => 2],
            ['id' => '3', 'name' => 'Jack', 'email' => 'jack@example.com', 'photoId' => 3],
        ]);
        $repository = new UserWithPhotoRepository($userWithPhotoDataProvider);
        $collection = $repository->getCollection(new QueryCriteria());
        $this->assertEquals(3, $collection->count());

        $iterator = $collection->getIterator();
        $this->assertEquals(1, $iterator->current()->photoId);
        $this->assertEmpty($iterator->current()->photoModel ?? null);

        $iterator->next();
        $this->assertEquals(2, $iterator->current()->photoId);
        $this->assertEmpty($iterator->current()->photoModel ?? null);

        $iterator->next();
        $this->assertEquals(3, $iterator->current()->photoId);
        $this->assertEmpty($iterator->current()->photoModel ?? null);

        $dataProvider = new ArrayDataProvider([
            ['id' => 1, 'filename' => 'photo1.jpg'],
            ['id' => 2, 'filename' => 'photo2.jpg'],
            ['id' => 3, 'filename' => 'photo3.jpg'],
        ]);

        $fetcher = RepositoryFetcherBuilder::init(new PhotoRepository($dataProvider))
            ->setFillingKeyName('photoModel')
            ->setForeignKeyName('photoId')
            ->setDestinationKeyName('id')
            ->build();
        $fetcher->fill($collection);

        $iterator = $collection->getIterator();
        $this->assertEquals(1, $iterator->current()->photoId);
        $photoModel = $iterator->current()->photoModel;
        $this->assertNotEmpty($photoModel);
        $this->assertInstanceOf(PhotoModel::class, $photoModel);
        $this->assertEquals(['id' => 1, 'filename' => 'photo1.jpg'], $photoModel->jsonSerialize());

        $iterator->next();
        $this->assertEquals(2, $iterator->current()->photoId);
        $photoModel = $iterator->current()->photoModel;
        $this->assertNotEmpty($photoModel);
        $this->assertInstanceOf(PhotoModel::class, $photoModel);
        $this->assertEquals(['id' => 2, 'filename' => 'photo2.jpg'], $photoModel->jsonSerialize());

        $iterator->next();
        $this->assertEquals(3, $iterator->current()->photoId);
        $photoModel = $iterator->current()->photoModel;
        $this->assertNotEmpty($photoModel);
        $this->assertInstanceOf(PhotoModel::class, $photoModel);
        $this->assertEquals(['id' => 3, 'filename' => 'photo3.jpg'], $photoModel->jsonSerialize());
    }

    /**
     * @throws Exception
     */
    public function testGetCollectionWithFetcher()
    {
        $userWithPhotoDataProvider = new ArrayDataProvider([
            ['id' => '1', 'name' => 'John', 'email' => 'john@example.com', 'photoId' => 1],
            ['id' => '2', 'name' => 'Jane', 'email' => 'jane@example.com', 'photoId' => 2],
            ['id' => '3', 'name' => 'Jack', 'email' => 'jack@example.com', 'photoId' => 3],
        ]);

        $photoDataProvider = new ArrayDataProvider([
            ['id' => 1, 'filename' => 'photo1.jpg'],
            ['id' => 2, 'filename' => 'photo2.jpg'],
            ['id' => 3, 'filename' => 'photo3.jpg'],
        ]);

        $repository= new UserWithPhotoRepository($userWithPhotoDataProvider, $photoDataProvider);
        $collection = $repository->getCollection(new QueryCriteria(), null, 'photoModel');
        $this->assertEquals(3, $collection->count());

        $iterator = $collection->getIterator();
        $this->assertEquals(1, $iterator->current()->photoId);
        $photoModel = $iterator->current()->photoModel;
        $this->assertNotEmpty($photoModel);
        $this->assertInstanceOf(PhotoModel::class, $photoModel);
        $this->assertEquals(['id' => 1, 'filename' => 'photo1.jpg'], $photoModel->jsonSerialize());

        $iterator->next();
        $this->assertEquals(2, $iterator->current()->photoId);
        $photoModel = $iterator->current()->photoModel;
        $this->assertNotEmpty($photoModel);
        $this->assertInstanceOf(PhotoModel::class, $photoModel);
        $this->assertEquals(['id' => 2, 'filename' => 'photo2.jpg'], $photoModel->jsonSerialize());

        $iterator->next();
        $this->assertEquals(3, $iterator->current()->photoId);
        $photoModel = $iterator->current()->photoModel;
        $this->assertNotEmpty($photoModel);
        $this->assertInstanceOf(PhotoModel::class, $photoModel);
        $this->assertEquals(['id' => 3, 'filename' => 'photo3.jpg'], $photoModel->jsonSerialize());
    }

    /**
     * @throws Exception
     */
    public function testFillMultipleValue()
    {
        $userWithPhotoDataProvider = new ArrayDataProvider([
            ['id' => '1', 'name' => 'John', 'email' => 'john@example.com', 'photoId' => 1],
            ['id' => '2', 'name' => 'Jane', 'email' => 'jane@example.com', 'photoId' => 2],
            ['id' => '3', 'name' => 'Jack', 'email' => 'jack@example.com', 'photoId' => 3],
        ]);
        $repository= new UserWithPhotoRepository($userWithPhotoDataProvider);
        $collection = $repository->getCollection(new QueryCriteria());
        $this->assertEquals(3, $collection->count());

        $iterator = $collection->getIterator();
        $this->assertEquals('1', $iterator->current()->id);
        $this->assertEmpty($iterator->current()->otherDocsCollection ?? null);

        $iterator->next();
        $this->assertEquals('2', $iterator->current()->id);
        $this->assertEmpty($iterator->current()->otherDocsCollection ?? null);

        $iterator->next();
        $this->assertEquals('3', $iterator->current()->id);
        $this->assertEmpty($iterator->current()->otherDocsCollection   ?? null);

        $dataProvider = new ArrayDataProvider([
            ['id' => 1, 'docPath' => 'doc1.jpg', 'userId' => 1],
            ['id' => 2, 'docPath' => 'doc2.jpg', 'userId' => 1],
            ['id' => 3, 'docPath' => 'doc3.jpg', 'userId' => 1],
            ['id' => 4, 'docPath' => 'doc4.jpg', 'userId' => 3],
            ['id' => 5, 'docPath' => 'doc5.jpg', 'userId' => 3],
        ]);

        $fetcher = RepositoryFetcherBuilder::init(new DocRepository($dataProvider), true)
            ->setFillingKeyName('otherDocsCollection')
            ->setForeignKeyName('id')
            ->setDestinationKeyName('userId')
            ->build();
        $fetcher->fill($collection);

        $iterator = $collection->getIterator();
        $this->assertEquals('1', $iterator->current()->id);
        $this->assertNotEmpty($iterator->current()->otherDocsCollection ?? null);
        $this->assertInstanceOf(CollectionInterface::class, $iterator->current()->otherDocsCollection);
        $this->assertEquals([
            ['id' => 1, 'docPath' => 'doc1.jpg', 'userId' => 1],
            ['id' => 2, 'docPath' => 'doc2.jpg', 'userId' => 1],
            ['id' => 3, 'docPath' => 'doc3.jpg', 'userId' => 1],
        ], $iterator->current()->otherDocsCollection->jsonSerialize());

        $iterator->next();
        $this->assertEquals('2', $iterator->current()->id);
        $this->assertEmpty($iterator->current()->otherDocsCollection ?? null);

        $iterator->next();
        $this->assertEquals('3', $iterator->current()->id);
        $this->assertNotEmpty($iterator->current()->otherDocsCollection   ?? null);
        $this->assertInstanceOf(CollectionInterface::class, $iterator->current()->otherDocsCollection);
        $this->assertEquals([
            ['id' => 4, 'docPath' => 'doc4.jpg', 'userId' => 3],
            ['id' => 5, 'docPath' => 'doc5.jpg', 'userId' => 3],
        ], $iterator->current()->otherDocsCollection->jsonSerialize());
    }

    /**
     * @throws Exception
     */
    public function testFillRevMultipleValue()
    {
        $userWithPhotoDataProvider = new ArrayDataProvider([
            ['id' => '1', 'linkedProfileIds' => [1, 2, 3]],
            ['id' => '2', 'linkedProfileIds' => [4, 5]],
            ['id' => '3', 'linkedProfileIds' => [6, 7]],
        ]);
        $repository= new UserWithPhotoRepository($userWithPhotoDataProvider);
        $collection = $repository->getCollection(new QueryCriteria());
        $this->assertEquals(3, $collection->count());

        $iterator = $collection->getIterator();
        $this->assertEquals('1', $iterator->current()->id);
        $this->assertEmpty($iterator->current()->linkedProfileCollection ?? null);

        $iterator->next();
        $this->assertEquals('2', $iterator->current()->id);
        $this->assertEmpty($iterator->current()->linkedProfileCollection ?? null);

        $iterator->next();
        $this->assertEquals('3', $iterator->current()->id);
        $this->assertEmpty($iterator->current()->linkedProfileCollection   ?? null);

        $dataProvider = new ArrayDataProvider([
            ['id' => 1, 'name' => 'profile1'],
            ['id' => 2, 'name' => 'profile2'],
            ['id' => 3, 'name' => 'profile3'],
            ['id' => 4, 'name' => 'profile4'],
            ['id' => 5, 'name' => 'profile5'],
            ['id' => 6, 'name' => 'profile6'],
            ['id' => 7, 'name' => 'profile7'],
        ]);

        $fetcher = RepositoryFetcherBuilder::init(new ProfileRepository($dataProvider), true)
            ->setFillingKeyName('linkedProfileCollection')
            ->setForeignKeyName('linkedProfileIds')
            ->setDestinationKeyName('id')
            ->build();
        $fetcher->fill($collection);

        $iterator = $collection->getIterator();
        $this->assertEquals('1', $iterator->current()->id);
        $this->assertNotEmpty($iterator->current()->linkedProfileCollection ?? null);
        $this->assertInstanceOf(CollectionInterface::class, $iterator->current()->linkedProfileCollection);
        $this->assertEquals([
            ['id' => 1, 'name' => 'profile1'],
            ['id' => 2, 'name' => 'profile2'],
            ['id' => 3, 'name' => 'profile3']
        ], $iterator->current()->linkedProfileCollection->jsonSerialize());

        $iterator->next();
        $this->assertEquals('2', $iterator->current()->id);
        $this->assertNotEmpty($iterator->current()->linkedProfileCollection ?? null);
        $this->assertInstanceOf(CollectionInterface::class, $iterator->current()->linkedProfileCollection);
        $this->assertEquals([
            ['id' => 4, 'name' => 'profile4'],
            ['id' => 5, 'name' => 'profile5']
        ], $iterator->current()->linkedProfileCollection->jsonSerialize());

        $iterator->next();
        $this->assertEquals('3', $iterator->current()->id);
        $this->assertNotEmpty($iterator->current()->linkedProfileCollection   ?? null);
        $this->assertInstanceOf(CollectionInterface::class, $iterator->current()->linkedProfileCollection);
        $this->assertEquals([
            ['id' => 6, 'name' => 'profile6'],
            ['id' => 7, 'name' => 'profile7']
        ], $iterator->current()->linkedProfileCollection->jsonSerialize());
    }

    /**
     * @throws Exception
     */
    public function testFillRevMultipleValueWithQuery()
    {
        $userWithPhotoDataProvider = new ArrayDataProvider([
            ['id' => '1', 'linkedProfileIds' => [1, 2, 3]],
            ['id' => '2', 'linkedProfileIds' => [4, 5]],
            ['id' => '3', 'linkedProfileIds' => [6, 7]],
        ]);
        $repository= new UserWithPhotoRepository($userWithPhotoDataProvider);
        $collection = $repository->getCollection(new QueryCriteria());
        $this->assertEquals(3, $collection->count());

        $iterator = $collection->getIterator();
        $this->assertEquals('1', $iterator->current()->id);
        $this->assertEmpty($iterator->current()->linkedProfileCollection ?? null);

        $iterator->next();
        $this->assertEquals('2', $iterator->current()->id);
        $this->assertEmpty($iterator->current()->linkedProfileCollection ?? null);

        $iterator->next();
        $this->assertEquals('3', $iterator->current()->id);
        $this->assertEmpty($iterator->current()->linkedProfileCollection   ?? null);

        $dataProvider = new ArrayDataProvider([
            ['id' => 1, 'name' => 'profile1'],
            ['id' => 2, 'name' => 'profile2'],
            ['id' => 3, 'name' => 'profile3'],
            ['id' => 4, 'name' => 'profile4'],
            ['id' => 5, 'name' => 'profile5'],
            ['id' => 6, 'name' => 'profile6'],
            ['id' => 7, 'name' => 'profile7'],
        ]);

        $query = new QueryCriteria();
        $query->addCriteria('name', CompareRuleInterface::IN, ['profile1', 'profile4', 'profile7']);
        $fetcher = RepositoryFetcherBuilder::init(new ProfileRepository($dataProvider), true)
            ->setFillingKeyName('linkedProfileCollection')
            ->setForeignKeyName('linkedProfileIds')
            ->setDestinationKeyName('id')
            ->setQuery($query)
            ->build();
        $fetcher->fill($collection);

        $iterator = $collection->getIterator();
        $this->assertEquals('1', $iterator->current()->id);
        $this->assertNotEmpty($iterator->current()->linkedProfileCollection ?? null);
        $this->assertEquals([
            ['id' => 1, 'name' => 'profile1'],
        ], $iterator->current()->linkedProfileCollection->jsonSerialize());

        $iterator->next();
        $this->assertEquals('2', $iterator->current()->id);
        $this->assertNotEmpty($iterator->current()->linkedProfileCollection ?? null);
        $this->assertEquals([
            ['id' => 4, 'name' => 'profile4'],
        ], $iterator->current()->linkedProfileCollection->jsonSerialize());

        $iterator->next();
        $this->assertEquals('3', $iterator->current()->id);
        $this->assertNotEmpty($iterator->current()->linkedProfileCollection   ?? null);
        $this->assertEquals([
            ['id' => 7, 'name' => 'profile7'],
        ], $iterator->current()->linkedProfileCollection->jsonSerialize());
    }


    public function testFillRevMultipleValueWithQueryLimit()
    {
        $userWithPhotoDataProvider = new ArrayDataProvider([
            ['id' => '1', 'linkedProfileIds' => [1, 2, 3]],
            ['id' => '2', 'linkedProfileIds' => [4, 5]],
            ['id' => '3', 'linkedProfileIds' => [6, 7]],
        ]);
        $repository= new UserWithPhotoRepository($userWithPhotoDataProvider);
        $collection = $repository->getCollection(new QueryCriteria());
        $this->assertEquals(3, $collection->count());

        $iterator = $collection->getIterator();
        $this->assertEquals('1', $iterator->current()->id);
        $this->assertEmpty($iterator->current()->linkedProfileCollection ?? null);

        $iterator->next();
        $this->assertEquals('2', $iterator->current()->id);
        $this->assertEmpty($iterator->current()->linkedProfileCollection ?? null);

        $iterator->next();
        $this->assertEquals('3', $iterator->current()->id);
        $this->assertEmpty($iterator->current()->linkedProfileCollection   ?? null);

        $dataProvider = new ArrayDataProvider([
            ['id' => 1, 'name' => 'profile1'],
            ['id' => 2, 'name' => 'profile2'],
            ['id' => 3, 'name' => 'profile3'],
            ['id' => 4, 'name' => 'profile4'],
            ['id' => 5, 'name' => 'profile5'],
            ['id' => 6, 'name' => 'profile6'],
            ['id' => 7, 'name' => 'profile7'],
        ]);

        $query = new QueryCriteria();
        $query->setLimit(4);
        $fetcher = RepositoryFetcherBuilder::init(new ProfileRepository($dataProvider), true)
            ->setFillingKeyName('linkedProfileCollection')
            ->setForeignKeyName('linkedProfileIds')
            ->setDestinationKeyName('id')
            ->setQuery($query)
            ->build();
        $fetcher->fill($collection);

        $iterator = $collection->getIterator();
        $this->assertEquals('1', $iterator->current()->id);
        $this->assertNotEmpty($iterator->current()->linkedProfileCollection ?? null);
        $this->assertEquals([
            ['id' => 1, 'name' => 'profile1'],
            ['id' => 2, 'name' => 'profile2'],
            ['id' => 3, 'name' => 'profile3']
        ], $iterator->current()->linkedProfileCollection->jsonSerialize());

        $iterator->next();
        $this->assertEquals('2', $iterator->current()->id);
        $this->assertNotEmpty($iterator->current()->linkedProfileCollection ?? null);
        $this->assertEquals([
            ['id' => 4, 'name' => 'profile4'],
        ], $iterator->current()->linkedProfileCollection->jsonSerialize());

        $iterator->next();
        $this->assertEquals('3', $iterator->current()->id);
        $this->assertEmpty($iterator->current()->linkedProfileCollection ?? null);
    }

    public function testFillRevMultipleValueWithQuerySelect()
    {
        $userWithPhotoDataProvider = new ArrayDataProvider([
            ['id' => '1', 'linkedProfileIds' => [1, 2, 3]],
            ['id' => '2', 'linkedProfileIds' => [4, 5]],
            ['id' => '3', 'linkedProfileIds' => [6, 7]],
        ]);
        $repository= new UserWithPhotoRepository($userWithPhotoDataProvider);
        $collection = $repository->getCollection(new QueryCriteria());
        $this->assertEquals(3, $collection->count());

        $iterator = $collection->getIterator();
        $this->assertEquals('1', $iterator->current()->id);
        $this->assertEmpty($iterator->current()->linkedProfileCollection ?? null);

        $iterator->next();
        $this->assertEquals('2', $iterator->current()->id);
        $this->assertEmpty($iterator->current()->linkedProfileCollection ?? null);

        $iterator->next();
        $this->assertEquals('3', $iterator->current()->id);
        $this->assertEmpty($iterator->current()->linkedProfileCollection   ?? null);

        $dataProvider = new ArrayDataProvider([
            ['id' => 1, 'name' => 'profile1'],
            ['id' => 2, 'name' => 'profile2'],
            ['id' => 3, 'name' => 'profile3'],
            ['id' => 4, 'name' => 'profile4'],
            ['id' => 5, 'name' => 'profile5'],
            ['id' => 6, 'name' => 'profile6'],
            ['id' => 7, 'name' => 'profile7'],
        ]);

        $query = new QueryCriteria();
        $query->setSelect(['id']);
        $fetcher = DataProviderFetcherBuilder::init($dataProvider, true)
            ->setFillingKeyName('linkedProfileCollection')
            ->setForeignKeyName('linkedProfileIds')
            ->setDestinationKeyName('id')
            ->setQuery($query)
            ->build();
        $fetcher->fill($collection);

        $iterator = $collection->getIterator();
        $this->assertEquals('1', $iterator->current()->id);
        $this->assertNotEmpty($iterator->current()->linkedProfileCollection ?? null);
        $this->assertEquals([
            ['id' => 1],
            ['id' => 2],
            ['id' => 3]
        ], $iterator->current()->linkedProfileCollection->jsonSerialize());

        $iterator->next();
        $this->assertEquals('2', $iterator->current()->id);
        $this->assertNotEmpty($iterator->current()->linkedProfileCollection ?? null);
        $this->assertEquals([
            ['id' => 4],
            ['id' => 5]
        ], $iterator->current()->linkedProfileCollection->jsonSerialize());

        $iterator->next();
        $this->assertEquals('3', $iterator->current()->id);
        $this->assertEquals([
            ['id' => 6],
            ['id' => 7]
        ], $iterator->current()->linkedProfileCollection->jsonSerialize());
    }

    public function testFillMultipleValueWithCallback()
    {
        $userWithPhotoDataProvider = new ArrayDataProvider([
            ['id' => '1', 'name' => 'John', 'email' => 'john@example.com', 'photoId' => 1],
            ['id' => '2', 'name' => 'Jane', 'email' => 'jane@example.com', 'photoId' => 2],
            ['id' => '3', 'name' => 'Jack', 'email' => 'jack@example.com', 'photoId' => 3],
        ]);
        $repository= new UserWithPhotoRepository($userWithPhotoDataProvider);
        $collection = $repository->getCollection(new QueryCriteria());
        $this->assertEquals(3, $collection->count());

        $iterator = $collection->getIterator();
        $this->assertEquals('1', $iterator->current()->id);
        $this->assertEmpty($iterator->current()->otherDocsCollection ?? null);

        $iterator->next();
        $this->assertEquals('2', $iterator->current()->id);
        $this->assertEmpty($iterator->current()->otherDocsCollection ?? null);

        $iterator->next();
        $this->assertEquals('3', $iterator->current()->id);
        $this->assertEmpty($iterator->current()->otherDocsCollection   ?? null);

        $dataProvider = new ArrayDataProvider([
            ['id' => 1, 'docPath' => 'doc1.jpg', 'userId' => 1],
            ['id' => 2, 'docPath' => 'doc2.jpg', 'userId' => 1],
            ['id' => 3, 'docPath' => 'doc3.jpg', 'userId' => 1],
            ['id' => 4, 'docPath' => 'doc4.jpg', 'userId' => 3],
            ['id' => 5, 'docPath' => 'doc5.jpg', 'userId' => 3],
        ]);

        $fetcher = RepositoryFetcherBuilder::init(new DocRepository($dataProvider), true)
            ->setCompareCallback(function (UserWithPhotoModel $model, DocModel $docModel): bool {
                return $model->id === $docModel->userId;
            })
            ->setItemFillCallback(function (UserWithPhotoModel $model, DocModel $docModel): void {
                $model->otherDocsCollection->append($docModel);
            })
            ->build();
        $fetcher->fill($collection);

        $iterator = $collection->getIterator();
        $this->assertEquals('1', $iterator->current()->id);
        $this->assertNotEmpty($iterator->current()->otherDocsCollection ?? null);
        $this->assertEquals([
            ['id' => 1, 'docPath' => 'doc1.jpg', 'userId' => 1],
            ['id' => 2, 'docPath' => 'doc2.jpg', 'userId' => 1],
            ['id' => 3, 'docPath' => 'doc3.jpg', 'userId' => 1],
        ], $iterator->current()->otherDocsCollection->jsonSerialize());

        $iterator->next();
        $this->assertEquals('2', $iterator->current()->id);
        $this->assertEmpty($iterator->current()->otherDocsCollection ?? null);

        $iterator->next();
        $this->assertEquals('3', $iterator->current()->id);
        $this->assertNotEmpty($iterator->current()->otherDocsCollection   ?? null);
        $this->assertEquals([
            ['id' => 4, 'docPath' => 'doc4.jpg', 'userId' => 3],
            ['id' => 5, 'docPath' => 'doc5.jpg', 'userId' => 3],
        ], $iterator->current()->otherDocsCollection->jsonSerialize());
    }
}
