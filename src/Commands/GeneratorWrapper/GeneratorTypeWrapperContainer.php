<?php

declare(strict_types=1);

namespace GraphQLProjection\Commands\GeneratorWrapper;

use Exception;
use GraphQLProjection\Commands\GeneratorWrapper\Wrappers\GeneratorInputObjectTypeBuilderWrapper;
use GraphQLProjection\Commands\GeneratorWrapper\Wrappers\GeneratorInputObjectTypeWrapper;
use GraphQLProjection\Commands\GeneratorWrapper\Wrappers\GeneratorObjectTypeBuilderWrapper;
use GraphQLProjection\Commands\GeneratorWrapper\Wrappers\GeneratorObjectTypeWrapper;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\NamedType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\UnionType;
use Illuminate\Support\Str;

readonly class GeneratorTypeWrapperContainer implements GeneratorTypeWrapperContainerInterface
{
    public function __construct(protected GeneratorTypesContext $typesContext, protected Type&NamedType $type) {}

    public function shouldGenerate(): bool
    {
        $name = $this->type->name;

        //lighthouse special types
        if (Str::startsWith($name, '__')) {
            return false;
        }

        if ($name === 'Query') {
            return false;
        }

        if ($name === 'Mutation') {
            return false;
        }

        if ($name === 'Subscription') {
            return false;
        }

        return true;
    }

    /**
     * @return array<GeneratorTypeWrapper>
     *
     * @throws Exception
     */
    public function getWrappers(): array
    {
        if ($this->type instanceof UnionType) {
            return [];
        }

        if ($this->type instanceof InterfaceType) {
            return [];
        }

        if ($this->type instanceof EnumType) {
            return [];
        }

        if ($this->type instanceof ObjectType) {
            return [
                new GeneratorObjectTypeBuilderWrapper($this->typesContext, $this->type),
                new GeneratorObjectTypeWrapper($this->typesContext, $this->type),
            ];
        }

        if ($this->type instanceof InputObjectType) {
            return [
                new GeneratorInputObjectTypeBuilderWrapper($this->typesContext, $this->type),
                new GeneratorInputObjectTypeWrapper($this->typesContext, $this->type),
            ];
        }

        if ($this->type instanceof ScalarType) {
            return [];
        }

        throw new Exception('Type not supported: '.class_basename($this->type));
    }
}
