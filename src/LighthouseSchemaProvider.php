<?php

declare(strict_types=1);

namespace Fedir\GraphQLProjection;

use Fedir\GraphQLProjection\Commands\GraphQLSchemaProvider;
use GraphQL\Type\Schema;
use Nuwave\Lighthouse\Schema\AST\ASTCache;
use Nuwave\Lighthouse\Schema\SchemaBuilder;

readonly class LighthouseSchemaProvider implements GraphQLSchemaProvider
{
    public function __construct(
        private ASTCache $cache,
        private SchemaBuilder $schemaBuilder,
    ) {}

    public function beforeGeneration(): void
    {
        $this->cache->clear();
    }

    public function afterGeneration(): void {}

    public function getSchema(): Schema
    {
        return $this->schemaBuilder->schema();
    }
}
