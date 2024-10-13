<?php

namespace Repository\Base\Tests\Stubs;

use Collection\Base\Collection;
use Collection\Base\Interfaces\CollectionInterface;
use Model\Base\BaseSerializableModel;
use Model\Base\Interfaces\ModelInterface;
use Model\Base\Interfaces\SerializeStrategyInterface;
use Model\Base\ModelDataLoader;

class UserWithPhotoModel extends BaseSerializableModel
{
    public string $id = '';
    public string $name = '';
    public int $photoId = 0;
    public array $photoData = [];
    public ?PhotoModel $photoModel = null;
    public array $otherDocs = [];
    public CollectionInterface $otherDocsCollection;
    public array $linkedProfileIds = [];
    public array $linkedProfiles = [];
    public CollectionInterface $linkedProfileCollection;

    public function __construct(?SerializeStrategyInterface $serializeStrategy = null)
    {
        parent::__construct($serializeStrategy);
        $this->otherDocsCollection = new Collection();
        $this->linkedProfileCollection = new Collection();
    }

    public static function initFromArray(array $data): ModelInterface
    {
        $model = new UserWithPhotoModel();
        ModelDataLoader::loadData($model, $data);
        return $model;
    }
}
