<?php

namespace Repository\Base\Filters;

use Exception;
use Repository\Base\Interfaces\PkCriteriaInterface;

class PkCriteria
{
    /**
     * @throws Exception
     */
    public static function simple(mixed $value, string $fieldName = 'id'): PkCriteriaInterface
    {
        if (empty($value)) {
            throw new Exception('Не задано значение первичного ключа');
        }
        return new SimplePk($value, $fieldName);
    }

    /**
     * @throws Exception
     */
    public static function complex(array $complexFilter): PkCriteriaInterface
    {
        if (empty($complexFilter)) {
            throw new Exception('Не задано значение составного ключа');
        }
        return new ComplexPk($complexFilter);
    }
}
