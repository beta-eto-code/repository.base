<?php

namespace Repository\Base\Fetchers\Tests;

use Data\Provider\Interfaces\CompareRuleInterface;
use Data\Provider\Providers\ArrayDataProvider;
use Data\Provider\QueryCriteria;
use Exception;
use PHPUnit\Framework\TestCase;
use Repository\Base\Fetchers\DataProviderFetcherBuilder;
use Repository\Base\Tests\Stubs\UserWithPhotoModel;
use Repository\Base\Tests\Stubs\UserWithPhotoRepository;

class DataProviderFetcherTest extends TestCase
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
        $repository= new UserWithPhotoRepository($userWithPhotoDataProvider);
        $collection = $repository->getCollection(new QueryCriteria());
        $this->assertEquals(3, $collection->count());

        $iterator = $collection->getIterator();
        $this->assertEquals(1, $iterator->current()->photoId);
        $this->assertEmpty($iterator->current()->photoData ?? null);

        $iterator->next();
        $this->assertEquals(2, $iterator->current()->photoId);
        $this->assertEmpty($iterator->current()->photoData ?? null);

        $iterator->next();
        $this->assertEquals(3, $iterator->current()->photoId);
        $this->assertEmpty($iterator->current()->photoData ?? null);

        $dataProvider = new ArrayDataProvider([
            ['id' => 1, 'filename' => 'photo1.jpg'],
            ['id' => 2, 'filename' => 'photo2.jpg'],
            ['id' => 3, 'filename' => 'photo3.jpg'],
        ]);

        $fetcher = DataProviderFetcherBuilder::init($dataProvider)
            ->setFillingKeyName('photoData')
            ->setForeignKeyName('photoId')
            ->setDestinationKeyName('id')
            ->build();
        $fetcher->fill($collection);

        $iterator = $collection->getIterator();
        $this->assertEquals(1, $iterator->current()->photoId);
        $photoData = $iterator->current()->photoData;
        $this->assertNotEmpty($photoData);
        $this->assertEquals(['id' => 1, 'filename' => 'photo1.jpg'], $photoData);

        $iterator->next();
        $this->assertEquals(2, $iterator->current()->photoId);
        $photoData = $iterator->current()->photoData;
        $this->assertNotEmpty($photoData);
        $this->assertEquals(['id' => 2, 'filename' => 'photo2.jpg'], $photoData);

        $iterator->next();
        $this->assertEquals(3, $iterator->current()->photoId);
        $photoData = $iterator->current()->photoData;
        $this->assertNotEmpty($photoData);
        $this->assertEquals(['id' => 3, 'filename' => 'photo3.jpg'], $photoData);
    }

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
        $collection = $repository->getCollection(new QueryCriteria(), null, 'photoData');
        $this->assertEquals(3, $collection->count());

        $iterator = $collection->getIterator();
        $this->assertEquals(1, $iterator->current()->photoId);
        $photoData = $iterator->current()->photoData;
        $this->assertNotEmpty($photoData);
        $this->assertEquals(['id' => 1, 'filename' => 'photo1.jpg'], $photoData);

        $iterator->next();
        $this->assertEquals(2, $iterator->current()->photoId);
        $photoData = $iterator->current()->photoData;
        $this->assertNotEmpty($photoData);
        $this->assertEquals(['id' => 2, 'filename' => 'photo2.jpg'], $photoData);

        $iterator->next();
        $this->assertEquals(3, $iterator->current()->photoId);
        $photoData = $iterator->current()->photoData;
        $this->assertNotEmpty($photoData);
        $this->assertEquals(['id' => 3, 'filename' => 'photo3.jpg'], $photoData);
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
        $this->assertEmpty($iterator->current()->otherDocs ?? null);

        $iterator->next();
        $this->assertEquals('2', $iterator->current()->id);
        $this->assertEmpty($iterator->current()->otherDocs ?? null);

        $iterator->next();
        $this->assertEquals('3', $iterator->current()->id);
        $this->assertEmpty($iterator->current()->otherDocs   ?? null);

        $dataProvider = new ArrayDataProvider([
            ['id' => 1, 'docPath' => 'doc1.jpg', 'userId' => 1],
            ['id' => 2, 'docPath' => 'doc2.jpg', 'userId' => 1],
            ['id' => 3, 'docPath' => 'doc3.jpg', 'userId' => 1],
            ['id' => 4, 'docPath' => 'doc4.jpg', 'userId' => 3],
            ['id' => 5, 'docPath' => 'doc5.jpg', 'userId' => 3],
        ]);

        $fetcher = DataProviderFetcherBuilder::init($dataProvider, true)
            ->setFillingKeyName('otherDocs')
            ->setForeignKeyName('id')
            ->setDestinationKeyName('userId')
            ->build();
        $fetcher->fill($collection);

        $iterator = $collection->getIterator();
        $this->assertEquals('1', $iterator->current()->id);
        $this->assertNotEmpty($iterator->current()->otherDocs ?? null);
        $this->assertEquals([
            ['id' => 1, 'docPath' => 'doc1.jpg', 'userId' => 1],
            ['id' => 2, 'docPath' => 'doc2.jpg', 'userId' => 1],
            ['id' => 3, 'docPath' => 'doc3.jpg', 'userId' => 1],
        ], $iterator->current()->otherDocs);

        $iterator->next();
        $this->assertEquals('2', $iterator->current()->id);
        $this->assertEmpty($iterator->current()->otherDocs ?? null);

        $iterator->next();
        $this->assertEquals('3', $iterator->current()->id);
        $this->assertNotEmpty($iterator->current()->otherDocs   ?? null);
        $this->assertEquals([
            ['id' => 4, 'docPath' => 'doc4.jpg', 'userId' => 3],
            ['id' => 5, 'docPath' => 'doc5.jpg', 'userId' => 3],
        ], $iterator->current()->otherDocs);
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
        $this->assertEmpty($iterator->current()->linkedProfiles ?? null);

        $iterator->next();
        $this->assertEquals('2', $iterator->current()->id);
        $this->assertEmpty($iterator->current()->linkedProfiles ?? null);

        $iterator->next();
        $this->assertEquals('3', $iterator->current()->id);
        $this->assertEmpty($iterator->current()->linkedProfiles   ?? null);

        $dataProvider = new ArrayDataProvider([
            ['id' => 1, 'name' => 'profile1'],
            ['id' => 2, 'name' => 'profile2'],
            ['id' => 3, 'name' => 'profile3'],
            ['id' => 4, 'name' => 'profile4'],
            ['id' => 5, 'name' => 'profile5'],
            ['id' => 6, 'name' => 'profile6'],
            ['id' => 7, 'name' => 'profile7'],
        ]);

        $fetcher = DataProviderFetcherBuilder::init($dataProvider, true)
            ->setFillingKeyName('linkedProfiles')
            ->setForeignKeyName('linkedProfileIds')
            ->setDestinationKeyName('id')
            ->build();
        $fetcher->fill($collection);

        $iterator = $collection->getIterator();
        $this->assertEquals('1', $iterator->current()->id);
        $this->assertNotEmpty($iterator->current()->linkedProfiles ?? null);
        $this->assertEquals([
            ['id' => 1, 'name' => 'profile1'],
            ['id' => 2, 'name' => 'profile2'],
            ['id' => 3, 'name' => 'profile3']
        ], $iterator->current()->linkedProfiles);

        $iterator->next();
        $this->assertEquals('2', $iterator->current()->id);
        $this->assertNotEmpty($iterator->current()->linkedProfiles ?? null);
        $this->assertEquals([
            ['id' => 4, 'name' => 'profile4'],
            ['id' => 5, 'name' => 'profile5']
        ], $iterator->current()->linkedProfiles);

        $iterator->next();
        $this->assertEquals('3', $iterator->current()->id);
        $this->assertNotEmpty($iterator->current()->linkedProfiles   ?? null);
        $this->assertEquals([
            ['id' => 6, 'name' => 'profile6'],
            ['id' => 7, 'name' => 'profile7']
        ], $iterator->current()->linkedProfiles);
    }

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
        $this->assertEmpty($iterator->current()->linkedProfiles ?? null);

        $iterator->next();
        $this->assertEquals('2', $iterator->current()->id);
        $this->assertEmpty($iterator->current()->linkedProfiles ?? null);

        $iterator->next();
        $this->assertEquals('3', $iterator->current()->id);
        $this->assertEmpty($iterator->current()->linkedProfiles   ?? null);

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
        $fetcher = DataProviderFetcherBuilder::init($dataProvider, true)
            ->setFillingKeyName('linkedProfiles')
            ->setForeignKeyName('linkedProfileIds')
            ->setDestinationKeyName('id')
            ->setQuery($query)
            ->build();
        $fetcher->fill($collection);

        $iterator = $collection->getIterator();
        $this->assertEquals('1', $iterator->current()->id);
        $this->assertNotEmpty($iterator->current()->linkedProfiles ?? null);
        $this->assertEquals([
            ['id' => 1, 'name' => 'profile1'],
        ], $iterator->current()->linkedProfiles);

        $iterator->next();
        $this->assertEquals('2', $iterator->current()->id);
        $this->assertNotEmpty($iterator->current()->linkedProfiles ?? null);
        $this->assertEquals([
            ['id' => 4, 'name' => 'profile4'],
        ], $iterator->current()->linkedProfiles);

        $iterator->next();
        $this->assertEquals('3', $iterator->current()->id);
        $this->assertNotEmpty($iterator->current()->linkedProfiles   ?? null);
        $this->assertEquals([
            ['id' => 7, 'name' => 'profile7'],
        ], $iterator->current()->linkedProfiles);
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
        $this->assertEmpty($iterator->current()->linkedProfiles ?? null);

        $iterator->next();
        $this->assertEquals('2', $iterator->current()->id);
        $this->assertEmpty($iterator->current()->linkedProfiles ?? null);

        $iterator->next();
        $this->assertEquals('3', $iterator->current()->id);
        $this->assertEmpty($iterator->current()->linkedProfiles   ?? null);

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
        $fetcher = DataProviderFetcherBuilder::init($dataProvider, true)
            ->setFillingKeyName('linkedProfiles')
            ->setForeignKeyName('linkedProfileIds')
            ->setDestinationKeyName('id')
            ->setQuery($query)
            ->build();
        $fetcher->fill($collection);

        $iterator = $collection->getIterator();
        $this->assertEquals('1', $iterator->current()->id);
        $this->assertNotEmpty($iterator->current()->linkedProfiles ?? null);
        $this->assertEquals([
            ['id' => 1, 'name' => 'profile1'],
            ['id' => 2, 'name' => 'profile2'],
            ['id' => 3, 'name' => 'profile3']
        ], $iterator->current()->linkedProfiles);

        $iterator->next();
        $this->assertEquals('2', $iterator->current()->id);
        $this->assertNotEmpty($iterator->current()->linkedProfiles ?? null);
        $this->assertEquals([
            ['id' => 4, 'name' => 'profile4'],
        ], $iterator->current()->linkedProfiles);

        $iterator->next();
        $this->assertEquals('3', $iterator->current()->id);
        $this->assertEmpty($iterator->current()->linkedProfiles   ?? null);
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
        $this->assertEmpty($iterator->current()->linkedProfiles ?? null);

        $iterator->next();
        $this->assertEquals('2', $iterator->current()->id);
        $this->assertEmpty($iterator->current()->linkedProfiles ?? null);

        $iterator->next();
        $this->assertEquals('3', $iterator->current()->id);
        $this->assertEmpty($iterator->current()->linkedProfiles   ?? null);

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
            ->setFillingKeyName('linkedProfiles')
            ->setForeignKeyName('linkedProfileIds')
            ->setDestinationKeyName('id')
            ->setQuery($query)
            ->build();
        $fetcher->fill($collection);

        $iterator = $collection->getIterator();
        $this->assertEquals('1', $iterator->current()->id);
        $this->assertNotEmpty($iterator->current()->linkedProfiles ?? null);
        $this->assertEquals([
            ['id' => 1],
            ['id' => 2],
            ['id' => 3]
        ], $iterator->current()->linkedProfiles);

        $iterator->next();
        $this->assertEquals('2', $iterator->current()->id);
        $this->assertNotEmpty($iterator->current()->linkedProfiles ?? null);
        $this->assertEquals([
            ['id' => 4],
            ['id' => 5]
        ], $iterator->current()->linkedProfiles);

        $iterator->next();
        $this->assertEquals('3', $iterator->current()->id);
        $this->assertEquals([
            ['id' => 6],
            ['id' => 7]
        ], $iterator->current()->linkedProfiles);
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
        $this->assertEmpty($iterator->current()->otherDocs ?? null);

        $iterator->next();
        $this->assertEquals('2', $iterator->current()->id);
        $this->assertEmpty($iterator->current()->otherDocs ?? null);

        $iterator->next();
        $this->assertEquals('3', $iterator->current()->id);
        $this->assertEmpty($iterator->current()->otherDocs   ?? null);

        $dataProvider = new ArrayDataProvider([
            ['id' => 1, 'docPath' => 'doc1.jpg', 'userId' => 1],
            ['id' => 2, 'docPath' => 'doc2.jpg', 'userId' => 1],
            ['id' => 3, 'docPath' => 'doc3.jpg', 'userId' => 1],
            ['id' => 4, 'docPath' => 'doc4.jpg', 'userId' => 3],
            ['id' => 5, 'docPath' => 'doc5.jpg', 'userId' => 3],
        ]);

        $fetcher = DataProviderFetcherBuilder::init($dataProvider, true)
            ->setCompareCallback(function (UserWithPhotoModel $model, array $docData): bool {
                return (int) $model->id === ($docData['userId'] ?? null);
            })
            ->setItemFillCallback(function (UserWithPhotoModel $model, array $docData): void {
                $model->otherDocs[] = ['dockId' => $docData['id'] ?? null, 'docPath' => $docData['docPath']];
            })
            ->build();
        $fetcher->fill($collection);

        $iterator = $collection->getIterator();
        $this->assertEquals('1', $iterator->current()->id);
        $this->assertNotEmpty($iterator->current()->otherDocs ?? null);
        $this->assertEquals([
            ['dockId' => 1, 'docPath' => 'doc1.jpg'],
            ['dockId' => 2, 'docPath' => 'doc2.jpg'],
            ['dockId' => 3, 'docPath' => 'doc3.jpg'],
        ], $iterator->current()->otherDocs);

        $iterator->next();
        $this->assertEquals('2', $iterator->current()->id);
        $this->assertEmpty($iterator->current()->otherDocs ?? null);

        $iterator->next();
        $this->assertEquals('3', $iterator->current()->id);
        $this->assertNotEmpty($iterator->current()->otherDocs   ?? null);
        $this->assertEquals([
            ['dockId' => 4, 'docPath' => 'doc4.jpg'],
            ['dockId' => 5, 'docPath' => 'doc5.jpg'],
        ], $iterator->current()->otherDocs);
    }
}
