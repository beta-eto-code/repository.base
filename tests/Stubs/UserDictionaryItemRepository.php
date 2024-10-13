<?php

namespace Repository\Base\Tests\Stubs;

use Repository\Base\BaseReadableRepository;

class UserDictionaryItemRepository extends BaseReadableRepository
{
    protected function getClassModel(): string
    {
        return UserDictionaryItemModel::class;
    }

    protected function getFetcherList(): array
    {
        return [];
    }
}
