<?php

declare(strict_types=1);

namespace GraphQLProjection\Client;

abstract class GraphQLQuery
{
    abstract public function getOperationName(): string;

    abstract public function getOperation(): string;

    public function __construct(public readonly array $fieldsSet = [])
    {
    }

    public function toArray(): array
    {
        $args = [];

        foreach ($this->fieldsSet as $field => $value) {
            if (is_object($value)) {
                $args[$field] = $this->extractFieldsFromObject($value);
            } else {
                $args[$field] = $value;
            }
        }

        return $args;
    }

    private function extractFieldsFromObject(mixed $value): array|string
    {
        return [];
    }
}
