<?php

declare(strict_types=1);

namespace GraphQLProjection\Client;

abstract class GraphQLQuery
{
    abstract public function getOperationName(): string;
}
