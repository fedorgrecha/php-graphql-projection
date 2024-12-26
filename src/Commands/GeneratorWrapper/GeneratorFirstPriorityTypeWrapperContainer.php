<?php

declare(strict_types=1);

namespace GraphQLProjection\Commands\GeneratorWrapper;

use Exception;
use GraphQLProjection\Commands\GeneratorWrapper\Wrappers\GeneratorConstantsWrapper;
use GraphQLProjection\Commands\GeneratorWrapper\Wrappers\GeneratorEnumTypeWrapper;
use GraphQLProjection\Commands\GeneratorWrapper\Wrappers\GeneratorInterfaceTypeWrapper;
use GraphQLProjection\Commands\GeneratorWrapper\Wrappers\GeneratorUnionTypeWrapper;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Type\Definition\UnionType;

readonly class GeneratorFirstPriorityTypeWrapperContainer extends GeneratorTypeWrapperContainer
{
    /**
     * @return array<GeneratorTypeWrapper>
     *
     * @throws Exception
     */
    public function getWrappers(): array
    {
        if ($this->type instanceof UnionType) {
            return [
                new GeneratorUnionTypeWrapper($this->typesContext, $this->type),
            ];
        }

        if ($this->type instanceof InterfaceType) {
            return [
                new GeneratorInterfaceTypeWrapper($this->typesContext, $this->type),
            ];
        }

        if ($this->type instanceof EnumType) {
            return [
                new GeneratorEnumTypeWrapper($this->typesContext, $this->type),
            ];
        }

        if ($this->type instanceof ObjectType) {
            return [
                new GeneratorConstantsWrapper($this->typesContext, $this->type),
            ];
        }

        if ($this->type instanceof InputObjectType) {
            return [];
        }

        if ($this->type instanceof ScalarType) {
            return [];
        }

        throw new Exception('Type not supported: '.class_basename($this->type));
    }
}
