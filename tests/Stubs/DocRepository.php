<?php

namespace Repository\Base\Tests\Stubs;

use Repository\Base\BaseReadableRepository;

class DocRepository extends BaseReadableRepository
{
    protected function getClassModel(): string
    {
        return DocModel::class;
    }

    protected function getFetcherList(): array
    {
        return [];
    }
}
