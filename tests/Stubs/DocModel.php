<?php

namespace Repository\Base\Tests\Stubs;

use Model\Base\BaseSerializableModel;
use Model\Base\Interfaces\ModelInterface;
use Model\Base\ModelDataLoader;

class DocModel extends BaseSerializableModel
{
    public int $id = 0;
    public string $docPath = '';
    public string $userId = '';

    public static function initFromArray(array $data): ModelInterface
    {
        $model = new DocModel();
        ModelDataLoader::loadData($model, $data);
        return $model;
    }
}
