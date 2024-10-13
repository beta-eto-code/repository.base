<?php

namespace Repository\Base\Tests;

use Collection\Base\Interfaces\CollectionInterface;
use Data\Provider\Interfaces\CompareRuleInterface;
use Data\Provider\Providers\ArrayDataProvider;
use Data\Provider\QueryCriteria;
use Exception;
use Generator;
use Iterator;
use PHPUnit\Framework\TestCase;
use Repository\Base\Filters\PkCriteria;
use Repository\Base\Tests\Stubs\ShortUserFactory;
use Repository\Base\Tests\Stubs\ShortUserModel;
use Repository\Base\Tests\Stubs\UserDictionaryItemModel;
use Repository\Base\Tests\Stubs\UserDictionaryItemRepository;
use Repository\Base\Tests\Stubs\UserModel;
use Repository\Base\Tests\Stubs\UserRepository;

class BaseReadableRepositoryTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testGetIterator()
    {
        $dataProvider = new ArrayDataProvider([
            ['id' => '1', 'name' => 'John', 'email' => 'john@example.com'],
            ['id' => '2', 'name' => 'Jane', 'email' => 'jane@example.com'],
            ['id' => '3', 'name' => 'Jack', 'email' => 'jack@example.com'],
        ]);
        $repository = new UserRepository($dataProvider);

        $iterator = $repository->getIterator(new QueryCriteria());
        $this->assertInstanceOf(Generator::class, $iterator);
        $this->assertCount(3, iterator_to_array($iterator));

        $iterator = $repository->getIterator(new QueryCriteria());
        $currentModel = $iterator->current();
        $this->assertInstanceOf(UserModel::class, $currentModel);
        $this->assertEquals('1', $currentModel->id);
        $this->assertEquals('John', $currentModel->name);

        $iterator->next();
        $currentModel = $iterator->current();
        $this->assertInstanceOf(UserModel::class, $currentModel);
        $this->assertEquals('2', $currentModel->id);
        $this->assertEquals('Jane', $currentModel->name);

        $iterator->next();
        $currentModel = $iterator->current();
        $this->assertInstanceOf(UserModel::class, $currentModel);
        $this->assertEquals('3', $currentModel->id);
        $this->assertEquals('Jack', $currentModel->name);
        $iterator->next();
        $this->assertFalse($iterator->valid());

        $query = new QueryCriteria();
        $query->addCriteria('name', CompareRuleInterface::IN, ['Jane', 'Jack']);
        $iterator = $repository->getIterator($query);
        $this->assertInstanceOf(Generator::class, $iterator);
        $this->assertCount(2, iterator_to_array($iterator));

        $iterator = $repository->getIterator($query);
        $query->addCriteria('name', CompareRuleInterface::IN, ['Jane', 'Jack']);
        $currentModel = $iterator->current();
        $this->assertInstanceOf(UserModel::class, $currentModel);
        $this->assertEquals('2', $currentModel->id);
        $this->assertEquals('Jane', $currentModel->name);

        $iterator->next();
        $currentModel = $iterator->current();
        $this->assertInstanceOf(UserModel::class, $currentModel);
        $this->assertEquals('3', $currentModel->id);
        $this->assertEquals('Jack', $currentModel->name);
        $iterator->next();
        $this->assertFalse($iterator->valid());
    }

    /**
     * @throws Exception
     */
    public function testGetCount()
    {
        $dataProvider = new ArrayDataProvider([
            ['id' => '1', 'name' => 'John', 'email' => 'john@example.com'],
            ['id' => '2', 'name' => 'Jane', 'email' => 'jane@example.com'],
            ['id' => '3', 'name' => 'Jack', 'email' => 'jack@example.com'],
        ]);
        $repository = new UserRepository($dataProvider);
        $this->assertEquals(3, $repository->getCount(new QueryCriteria()));

        $query = new QueryCriteria();
        $query->addCriteria('name', CompareRuleInterface::IN, ['Jane', 'Jack']);
        $this->assertEquals(2, $repository->getCount($query));

        $query = new QueryCriteria();
        $query->addCriteria('id', CompareRuleInterface::IN, ['1', '44']);
        $this->assertEquals(1, $repository->getCount($query));
    }

    /**
     * @throws Exception
     */
    public function testGetModelIterator()
    {
        $dataProvider = new ArrayDataProvider([
            ['id' => '1', 'name' => 'John', 'email' => 'john@example.com'],
            ['id' => '2', 'name' => 'Jane', 'email' => 'jane@example.com'],
            ['id' => '3', 'name' => 'Jack', 'email' => 'jack@example.com'],
        ]);
        $repository = new UserRepository($dataProvider);

        $iterator = $repository->getModelIterator(new ShortUserFactory(), new QueryCriteria());
        $this->assertInstanceOf(Generator::class, $iterator);
        $this->assertCount(3, iterator_to_array($iterator));

        $iterator = $repository->getModelIterator(new ShortUserFactory(), new QueryCriteria());
        $currentModel = $iterator->current();
        $this->assertInstanceOf(ShortUserModel::class, $currentModel);
        $this->assertEquals(1, $currentModel->id);

        $iterator->next();
        $currentModel = $iterator->current();
        $this->assertInstanceOf(ShortUserModel::class, $currentModel);
        $this->assertEquals(2, $currentModel->id);

        $iterator->next();
        $currentModel = $iterator->current();
        $this->assertInstanceOf(ShortUserModel::class, $currentModel);
        $this->assertEquals(3, $currentModel->id);

        $query = new QueryCriteria();
        $query->addCriteria('name', CompareRuleInterface::IN, ['Jane', 'Jack']);
        $iterator = $repository->getModelIterator(new ShortUserFactory(), $query);
        $this->assertInstanceOf(Generator::class, $iterator);
        $this->assertCount(2, iterator_to_array($iterator));

        $iterator = $repository->getModelIterator(new ShortUserFactory(), new QueryCriteria());
        $currentModel = $iterator->current();
        $this->assertInstanceOf(ShortUserModel::class, $currentModel);
        $this->assertEquals(1, $currentModel->id);

        $iterator->next();
        $currentModel = $iterator->current();
        $this->assertInstanceOf(ShortUserModel::class, $currentModel);
        $this->assertEquals(2, $currentModel->id);
    }

    /**
     * @throws Exception
     */
    public function testGetByPk()
    {
        $dataProvider = new ArrayDataProvider([
            ['id' => '1', 'name' => 'John', 'email' => 'john@example.com'],
            ['id' => '2', 'name' => 'Jane', 'email' => 'jane@example.com'],
            ['id' => '3', 'name' => 'Jack', 'email' => 'jack@example.com'],
        ]);

        $repository = new UserRepository($dataProvider);
        $model = $repository->getByPk(PkCriteria::simple('1', 'id'));
        $this->assertInstanceOf(UserModel::class, $model);
        $this->assertEquals('1', $model->id);
        $this->assertEquals('John', $model->name);

        $model = $repository->getByPk(PkCriteria::simple('2', 'id'));
        $this->assertInstanceOf(UserModel::class, $model);
        $this->assertEquals('2', $model->id);
        $this->assertEquals('Jane', $model->name);

        $dataProvider = new ArrayDataProvider([
            ['userId' => '1', 'dictionaryItemId' => '1', 'description' => 'item1'],
            ['userId' => '1', 'dictionaryItemId' => '2', 'description' => 'item2'],
            ['userId' => '3', 'dictionaryItemId' => '1', 'description' => 'item1'],
        ]);
        $repository = new UserDictionaryItemRepository($dataProvider);

        $model = $repository->getByPk(PkCriteria::complex(['userId' => '1', 'dictionaryItemId' => '1']));
        $this->assertInstanceOf(UserDictionaryItemModel::class, $model);
        $this->assertEquals('1', $model->userId);
        $this->assertEquals('1', $model->dictionaryItemId);
        $this->assertEquals('item1', $model->description);

        $model = $repository->getByPk(PkCriteria::complex(['userId' => '3', 'dictionaryItemId' => '1']));
        $this->assertInstanceOf(UserDictionaryItemModel::class, $model);
        $this->assertEquals('3', $model->userId);
        $this->assertEquals('1', $model->dictionaryItemId);
        $this->assertEquals('item1', $model->description);

        $model = $repository->getByPk(PkCriteria::complex(['userId' => '4', 'dictionaryItemId' => '1']));
        $this->assertEmpty($model);
    }

    /**
     * @throws Exception
     */
    public function testGetModelCollection()
    {
        $dataProvider = new ArrayDataProvider([
            ['id' => '1', 'name' => 'John', 'email' => 'john@example.com'],
            ['id' => '2', 'name' => 'Jane', 'email' => 'jane@example.com'],
            ['id' => '3', 'name' => 'Jack', 'email' => 'jack@example.com'],
        ]);
        $repository = new UserRepository($dataProvider);

        $collection = $repository->getModelCollection(new ShortUserFactory(), new QueryCriteria());
        $this->assertCount(3, $collection);
        $iterator = $collection->getIterator();

        $currentModel = $iterator->current();
        $this->assertInstanceOf(ShortUserModel::class, $currentModel);
        $this->assertEquals(1, $currentModel->id);

        $iterator->next();
        $currentModel = $iterator->current();
        $this->assertInstanceOf(ShortUserModel::class, $currentModel);
        $this->assertEquals(2, $currentModel->id);

        $iterator->next();
        $currentModel = $iterator->current();
        $this->assertInstanceOf(ShortUserModel::class, $currentModel);
        $this->assertEquals(3, $currentModel->id);

        $query = new QueryCriteria();
        $query->addCriteria('name', CompareRuleInterface::IN, ['Jane', 'Jack']);
        $collection = $repository->getModelCollection(new ShortUserFactory(), $query);
        $this->assertInstanceOf(CollectionInterface::class, $collection);
        $this->assertCount(2, $collection);
        $iterator = $collection->getIterator();

        $currentModel = $iterator->current();
        $this->assertInstanceOf(ShortUserModel::class, $currentModel);
        $this->assertEquals(2, $currentModel->id);

        $iterator->next();
        $currentModel = $iterator->current();
        $this->assertInstanceOf(ShortUserModel::class, $currentModel);
        $this->assertEquals(3, $currentModel->id);
    }

    /**
     * @throws Exception
     */
    public function testGetCollection()
    {
        $dataProvider = new ArrayDataProvider([
            ['id' => '1', 'name' => 'John', 'email' => 'john@example.com'],
            ['id' => '2', 'name' => 'Jane', 'email' => 'jane@example.com'],
            ['id' => '3', 'name' => 'Jack', 'email' => 'jack@example.com'],
        ]);
        $repository = new UserRepository($dataProvider);

        $collection = $repository->getCollection(new QueryCriteria());
        $this->assertCount(3, $collection);

        $iterator = $collection->getIterator();
        $currentModel = $iterator->current();
        $this->assertInstanceOf(UserModel::class, $currentModel);
        $this->assertEquals('1', $currentModel->id);
        $this->assertEquals('John', $currentModel->name);

        $iterator->next();
        $currentModel = $iterator->current();
        $this->assertInstanceOf(UserModel::class, $currentModel);
        $this->assertEquals('2', $currentModel->id);
        $this->assertEquals('Jane', $currentModel->name);

        $iterator->next();
        $currentModel = $iterator->current();
        $this->assertInstanceOf(UserModel::class, $currentModel);
        $this->assertEquals('3', $currentModel->id);
        $this->assertEquals('Jack', $currentModel->name);

        $query = new QueryCriteria();
        $query->addCriteria('name', CompareRuleInterface::IN, ['Jane', 'Jack']);
        $collection = $repository->getCollection($query);
        $this->assertInstanceOf(CollectionInterface::class, $collection);
        $this->assertCount('2', $collection);

        $iterator = $collection->getIterator();
        $currentModel = $iterator->current();
        $this->assertInstanceOf(UserModel::class, $currentModel);
        $this->assertEquals('2', $currentModel->id);
        $this->assertEquals('Jane', $currentModel->name);

        $iterator->next();
        $currentModel = $iterator->current();
        $this->assertInstanceOf(UserModel::class, $currentModel);
        $this->assertEquals('3', $currentModel->id);
        $this->assertEquals('Jack', $currentModel->name);
    }
}
