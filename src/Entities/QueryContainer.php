<?php

declare(strict_types=1);

namespace GraphQLProjection\Entities;

use ErrorException;
use GraphQL\Type\Definition\FieldDefinition;
use Illuminate\Support\Traits\ForwardsCalls;

/**
 * @mixin FieldDefinition
 */
readonly class QueryContainer
{
    use ForwardsCalls;

    public function __construct(
        public bool $isMutation,
        public FieldDefinition $query,
    ) {
    }

    public function __call(string $name, array $arguments)
    {
        return $this->forwardCallTo($this->query, $name, $arguments);
    }

    /** @throws ErrorException */
    public function __get(string $name)
    {
        try {
            return $this->query->{$name};
        } catch (ErrorException $e) {
            $pattern = '~^Undefined property: (?P<class>[^:]+)::\$(?P<property>.+)$~';

            if (! preg_match($pattern, $e->getMessage(), $matches)) {
                throw $e;
            }

            if ($matches['class'] != get_class($this->query) || $matches['property'] != $name) {
                throw $e;
            }

            $this->throwUndefinedPropertyException($name);
        }
    }

    /** @throws ErrorException */
    protected function throwUndefinedPropertyException(string $name): void
    {
        throw new ErrorException(
            sprintf('Undefined property: %s::$%s', self::class, $name),
        );
    }
}
