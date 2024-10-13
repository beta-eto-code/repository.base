<?php

namespace Repository\Base\Tests\Stubs;

use Model\Base\BaseSerializableModel;
use Model\Base\Interfaces\ModelInterface;

class ShortUserModel extends BaseSerializableModel
{
    public int $id;

    public static function initFromArray(array $data): ModelInterface
    {
        $model = new ShortUserModel();
        $model->id = (int) ($data['id'] ?? 0);

        return $model;
    }
}
