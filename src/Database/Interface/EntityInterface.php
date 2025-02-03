<?php

namespace Core\Database\Interface;

interface EntityInterface
{
    public static function getEntityName() : string;

    /**
     * @return class-string<EntityTableInterface>
     */
    public static function getTable() : string;
}
