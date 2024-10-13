<?php

namespace Repository\Base\Tests\Stubs;

use Repository\Base\BaseReadableRepository;

class ProfileRepository extends BaseReadableRepository
{
    protected function getClassModel(): string
    {
        return ProfileModel::class;
    }

    protected function getFetcherList(): array
    {
        return [];
    }
}
