<?php

declare(strict_types=1);

namespace GraphQLProjection\Client;

use GraphQLProjection\Client\Projection\BaseProjection;

class GraphQLQueryExecutor
{
    /**
     * @template TARGET of object
     *
     * @param  class-string<TARGET>  $class
     * @return TARGET
     */
    public static function execute(GraphQLQuery $query, BaseProjection $projection, string $class): mixed
    {
        //todo
        $queryString = self::serializeQuery($query);

        return new $class();
    }

    private static function serializeQuery(GraphQLQuery $query): string
    {
        return 'todo';
    }

    private static function serializeProjection(BaseProjection $projection): string
    {
        return 'todo';
    }
}
