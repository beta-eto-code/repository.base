<?php

namespace Repository\Base\Tests\Stubs;

use Model\Base\Interfaces\ModelInterface;
use Repository\Base\Interfaces\ModelFactoryInterface;

class ShortUserFactory implements ModelFactoryInterface
{
    public function createModelFromArray(array $data): ModelInterface
    {
        $model = new ShortUserModel();
        $model->id = (int) ($data['id'] ?? 0);
        return $model;
    }
}
