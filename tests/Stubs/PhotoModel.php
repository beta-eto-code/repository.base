<?php

namespace Repository\Base\Tests\Stubs;

use Model\Base\BaseSerializableModel;
use Model\Base\Interfaces\ModelInterface;
use Model\Base\ModelDataLoader;

class PhotoModel extends BaseSerializableModel
{
    public int $id = 0;
    public string $filename = '';

    public static function initFromArray(array $data): ModelInterface
    {
        $model = new PhotoModel();
        ModelDataLoader::loadData($model, $data);
        return $model;
    }
}
