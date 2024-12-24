<?php

declare(strict_types=1);

namespace Fedir\GraphQLProjection;

use Fedir\GraphQLProjection\Commands\GraphQLSchemaProvider;
use GraphQL\Type\Schema;
use Rebing\GraphQL\Support\Facades\GraphQL;

readonly class RebingSchemaProvider implements GraphQLSchemaProvider
{
    public function beforeGeneration(): void
    {
        GraphQL::clearSchemas();
    }

    public function afterGeneration(): void {}

    public function getSchema(): Schema
    {
        return GraphQL::schema();
    }
}
