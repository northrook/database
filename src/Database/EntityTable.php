<?php

namespace Core\Database;

use Core\Database\Interface\EntityTableInterface;
use function Support\classBasename;

abstract class EntityTable implements EntityTableInterface
{
    public static function getTableName() : string
    {
        return classBasename( static::class );
    }
}
