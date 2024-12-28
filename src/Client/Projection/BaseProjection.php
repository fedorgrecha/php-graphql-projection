<?php

declare(strict_types=1);

namespace GraphQLProjection\Client\Projection;

class BaseProjection
{
    protected array $fields = [];
    protected array $inputArguments = [];

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getInputArguments(): array
    {
        return $this->inputArguments;
    }
}
