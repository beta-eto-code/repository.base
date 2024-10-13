<?php

namespace Repository\Base\Tests\Stubs;

use Model\Base\BaseSerializableModel;
use Model\Base\Interfaces\ModelInterface;
use Model\Base\ModelDataLoader;

class ProfileModel extends BaseSerializableModel
{
    public int $id = 0;
    public string $name = '';

    public static function initFromArray(array $data): ModelInterface
    {
        $model = new ProfileModel();
        ModelDataLoader::loadData($model, $data);
        return $model;
    }
}
