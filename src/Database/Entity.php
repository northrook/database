<?php

namespace Core\Database;

use Core\Database\Interface\EntityInterface;
use function Support\classBasename;

abstract class Entity implements EntityInterface
{
    public static function getEntityName() : string
    {
        return classBasename( static::class );
    }
}
