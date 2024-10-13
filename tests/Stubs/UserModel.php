<?php

namespace Repository\Base\Tests\Stubs;

use Model\Base\BaseSerializableModel;
use Model\Base\Interfaces\ModelInterface;
use Model\Base\ModelDataLoader;

class UserModel extends BaseSerializableModel
{
    public string $id;
    public string $name;
    public string $email;

    public static function initFromArray(array $data): ModelInterface
    {
        $user = new UserModel();
        ModelDataLoader::loadData($user, $data);
        return $user;
    }
}
