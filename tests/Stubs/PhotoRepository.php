<?php

namespace Repository\Base\Tests\Stubs;

use Repository\Base\BaseReadableRepository;

class PhotoRepository extends BaseReadableRepository
{
    protected function getClassModel(): string
    {
        return PhotoModel::class;
    }

    protected function getFetcherList(): array
    {
        return [];
    }
}
