## Установка

```
composer require beta/repository.base
```

**Пример описания модели для репозитория**

```php
use Model\Base\BaseSerializableModel;
use Model\Base\Interfaces\ModelInterface;
use Model\Base\ModelDataLoader;

class UserModel extends BaseSerializableModel
{
    public int $id = 0;
    public string $name = '';
    public string $email = '';
    public int $photoId = 0;
    public ?PhotoModel $photoModel = null;

    public static function initFromArray(array $data): ModelInterface
    {
        $user = new UserModel();
        ModelDataLoader::loadData($user, $data);
        return $user;
    }
}
```

**Пример описания репозитория**

```php
use Collection\Base\Interfaces\CollectionItemInterface;
use Data\Provider\Interfaces\PkOperationResultInterface;
use Exception;
use Repository\Base\BaseRepository;
use Repository\Base\Filters\PkCriteria;
use Repository\Base\Interfaces\AccessRecipientContextInterface;
use Data\Provider\Interfaces\DataProviderInterface;
use Repository\Base\Interfaces\ReadableRepositoryInterface

class UserRepository extends BaseRepository
{
    private ReadableRepositoryInterface $photoRepository;
    
    public function __construct(DataProviderInterface $dataProvider, ReadableRepositoryInterface $photoRepository) {
        parent::__construct($dataProvider);
        $this->photoRepository = $photoRepository;
    }

    protected function getClassModel(): string
    {
        return UserModel::class;
    }

    /**
     * @throws Exception
     */
    public function save(
        CollectionItemInterface $item,
        ?AccessRecipientContextInterface $recipientContext = null
    ): PkOperationResultInterface {
        $id = $item->getValueByKey('id');
        $data = $item->jsonSerialize();
        $result = $this->dataProvider->save(
            $data,
            $id ? PkCriteria::simple($id, 'id')->createQuery() : null
        );
        $item->setValueByKey('id', $data['id'] ?: '');
        return $result;
    }

    protected function getFetcherList(): array
    {
        return [
            'photoModel' => $this->createPhotoModelFetcher()
        ];
    }
    
    /**
     * @throws Exception
     */
    private function createPhotoModelFetcher(): FetcherInterface
    {
        return RepositoryFetcherBuilder::init($this->photoRepository)
            ->setFillingKeyName('photoModel')
            ->setForeignKeyName('photoId')
            ->setDestinationKeyName('id')
            ->build();
    }
}
```

**Пример работы с репозиторием**
```php
use Data\Provider\QueryCriteria;
use Repository\Base\Filters\PkCriteria;
use Data\Provider\Interfaces\CompareRuleInterface;

$userRepository = new \Repository\Base\Tests\Stubs\UserRepository(new SomeUserDataProvider);
$userQuery = new QueryCriteria();
$userQuery->setLimit(100);
$userCollection = $userRepository->getCollection($userQuery); // коллекция пользователей не превышающая 100 элементов
$photoModelFetcher = current($userRepository->getFetcherListByNames('photoModel'));
$photoModelFetcher->fill($userCollection); // догружаем фотографии в коллекцию пользователей

$userCollection = $userRepository->getCollection($userQuery, null, 'photoModel'); // коллекция пользователей с фотографиями

foreach ($userRepository->getIterator($userQuery) as $userModel) {
// обработка моделей в цикле
}

$userModel = $userRepository->getByPk(PkCriteria::simple([1, 'id'])); // модель пользователя с id = 1
$userRepository->getModelCollection(new ShortUserFactory, $userQuery); // коллекция с альтернативными моделями

$deleteQuery = new QueryCriteria();
$deleteQuery->addCriteria('id', CompareRuleInterface::IN, [1, 2, 3]);
$userRepository->delete($deleteQuery); // удаляем пользователей с id 1, 2 и 3

$userRepository->deleteByPk(PkCriteria::simple(4, 'id')); // удаляем пользователя с id = 4

$userRepository->save($someUserModel); // сохраняем данные из произвольной модели пользователя
$userRepository->saveCollection($someUserCollection); // сохраняем данные моделей пользователей из произвольной коллекции
```