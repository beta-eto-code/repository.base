<?php

namespace Repository\Base\Interfaces;

use Model\Base\Interfaces\ModelInterface;

interface ModelFactoryInterface
{
    public function createModelFromArray(array $data): ModelInterface;
}
