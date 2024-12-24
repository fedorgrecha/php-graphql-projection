<?php

declare(strict_types=1);

namespace Fedir\GraphQLProjection\Commands\GeneratorWrapper;

use GraphQL\Type\Definition\NamedType;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Type\Definition\Type;

class GeneratorTypesContext
{
    private array $unionMap = [];

    /**
     * @param  array<string, Type&NamedType>  $types
     */
    public function __construct(private readonly array $types) {}

    public function isScalar(string $typeName): bool
    {
        if (! array_key_exists($typeName, $this->types)) {
            return false;
        }

        return $this->types[$typeName] instanceof ScalarType;
    }

    public function getType(string $typeName): Type&NamedType
    {
        /** @var Type&NamedType */
        return $this->types[$typeName];
    }

    public function addUnionUsageIn(string $unionName, array $types): void
    {
        foreach ($types as $type) {
            $this->unionMap[$type][] = $unionName;
        }
    }

    public function getUnionUsageIn(string $typeName): array
    {
        return $this->unionMap[$typeName] ?? [];
    }
}
