<?php

namespace Repository\Base\Tests;

use Collection\Base\Collection;
use Data\Provider\Interfaces\CompareRuleInterface;
use Data\Provider\Providers\ArrayDataProvider;
use Data\Provider\QueryCriteria;
use Exception;
use PHPUnit\Framework\TestCase;
use Repository\Base\Filters\PkCriteria;
use Repository\Base\Tests\Stubs\UserModel;
use Repository\Base\Tests\Stubs\UserRepository;

class BaseRepositoryTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testDeleteByPk()
    {
        $dataProvider = new ArrayDataProvider([
            ['id' => '1', 'name' => 'John', 'email' => 'john@example.com'],
            ['id' => '2', 'name' => 'Jane', 'email' => 'jane@example.com'],
            ['id' => '3', 'name' => 'Jack', 'email' => 'jack@example.com'],
        ]);
        $repository = new UserRepository($dataProvider);
        $this->assertCount(3, $repository->getCollection(new QueryCriteria()));

        $repository->deleteByPk(PkCriteria::simple('1', 'id'));
        $this->assertCount(2, $repository->getCollection(new QueryCriteria()));

        $repository->deleteByPk(PkCriteria::simple('4', 'id'));
        $this->assertCount(2, $repository->getCollection(new QueryCriteria()));

        $repository->deleteByPk(PkCriteria::simple('3', 'id'));
        $this->assertCount(1, $repository->getCollection(new QueryCriteria()));
    }

    /**
     * @throws Exception
     */
    public function testDelete()
    {
        $dataProvider = new ArrayDataProvider([
            ['id' => '1', 'name' => 'John', 'email' => 'john@example.com'],
            ['id' => '2', 'name' => 'Jane', 'email' => 'jane@example.com'],
            ['id' => '3', 'name' => 'Jack', 'email' => 'jack@example.com'],
        ]);
        $repository = new UserRepository($dataProvider);
        $this->assertCount(3, $repository->getCollection(new QueryCriteria()));

        $query = new QueryCriteria();
        $query->addCriteria('name', CompareRuleInterface::IN, ['John', 'Jane']);
        $repository->delete($query);
        $collection = $repository->getCollection(new QueryCriteria());
        $this->assertCount(1, $collection);
        $this->assertEquals('Jack', $collection->first()->name);
    }

    /**
     * @throws Exception
     */
    public function testSave()
    {
        $dataProvider = new ArrayDataProvider([
            ['id' => '1', 'name' => 'John', 'email' => 'john@example.com'],
            ['id' => '2', 'name' => 'Jane', 'email' => 'jane@example.com'],
            ['id' => '3', 'name' => 'Jack', 'email' => 'jack@example.com'],
        ], 'id');
        $repository = new UserRepository($dataProvider);
        $secondUser = UserModel::initFromArray(['id' => '2', 'name' => 'Jane', 'email' => 'jane@example.com']);
        $secondUser->name = 'Jane2';
        $repository->save($secondUser);
        $userFromRepository = $repository->getByPk(PkCriteria::simple('2', 'id'));
        $this->assertEquals('2', $userFromRepository->id);
        $this->assertEquals('Jane2', $userFromRepository->name);
    }

    /**
     * @throws Exception
     */
    public function testSaveCollection()
    {
        $dataProvider = new ArrayDataProvider([
            ['id' => '1', 'name' => 'John', 'email' => 'john@example.com'],
            ['id' => '2', 'name' => 'Jane', 'email' => 'jane@example.com'],
            ['id' => '3', 'name' => 'Jack', 'email' => 'jack@example.com'],
        ], 'id');
        $repository = new UserRepository($dataProvider);
        $this->assertCount(3, $repository->getCollection(new QueryCriteria()));

        $firstUser = UserModel::initFromArray(['id' => '1', 'name' => 'John', 'email' => 'john@example.com']);
        $firstUser->name = 'John2';
        $secondUser = UserModel::initFromArray(['id' => '2', 'name' => 'Jane', 'email' => 'jane@example.com']);
        $secondUser->name = 'Jane2';
        $thirdUser = UserModel::initFromArray(['id' => '3', 'name' => 'Jack', 'email' => 'jack@example.com']);
        $thirdUser->name = 'Jack2';

        $collectionForSave = new Collection([$firstUser, $secondUser, $thirdUser]);
        $repository->saveCollection($collectionForSave);

        $collectionFromRepository = $repository->getCollection(new QueryCriteria());
        $this->assertCount(3, $collectionFromRepository);
        $iterator = $collectionFromRepository->getIterator();
        $this->assertEquals('John2', $iterator->current()->name);

        $iterator->next();
        $this->assertEquals('Jane2', $iterator->current()->name);

        $iterator->next();
        $this->assertEquals('Jack2', $iterator->current()->name);
    }
}
