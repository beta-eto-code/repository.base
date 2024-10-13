<?php

namespace Repository\Base\Tests\Stubs;

use Collection\Base\Interfaces\CollectionItemInterface;
use Data\Provider\Interfaces\PkOperationResultInterface;
use Exception;
use Repository\Base\BaseRepository;
use Repository\Base\Filters\PkCriteria;
use Repository\Base\Interfaces\AccessRecipientContextInterface;

class UserRepository extends BaseRepository
{
    protected function getClassModel(): string
    {
        return UserModel::class;
    }

    /**
     * @throws Exception
     */
    public function save(
        CollectionItemInterface $item,
        ?AccessRecipientContextInterface $recipientContext = null
    ): PkOperationResultInterface {
        $id = $item->getValueByKey('id');
        $data = $item->jsonSerialize();
        $result = $this->dataProvider->save(
            $data,
            $id ? PkCriteria::simple($id, 'id')->createQuery() : null
        );
        $item->setValueByKey('id', $data['id'] ?: '');
        return $result;
    }

    protected function getFetcherList(): array
    {
        return [];
    }
}
