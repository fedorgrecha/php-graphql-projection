<?php

declare(strict_types=1);

namespace Fedir\GraphQLProjection\Commands;

use GraphQL\Type\Schema;

interface GraphQLSchemaProvider
{
    public function beforeGeneration(): void;

    public function afterGeneration(): void;

    public function getSchema(): Schema;
}
