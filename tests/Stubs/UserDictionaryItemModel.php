<?php

namespace Repository\Base\Tests\Stubs;

use Model\Base\BaseSerializableModel;
use Model\Base\Interfaces\ModelInterface;
use Model\Base\ModelDataLoader;

class UserDictionaryItemModel extends BaseSerializableModel
{
    public string $userId;
    public string $dictionaryItemId;
    public string $description;

    public static function initFromArray(array $data): ModelInterface
    {
        $model = new UserDictionaryItemModel();
        ModelDataLoader::loadData($model, $data);
        return $model;
    }
}
