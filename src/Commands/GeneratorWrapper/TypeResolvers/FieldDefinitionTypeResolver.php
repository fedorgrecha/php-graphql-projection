<?php

declare(strict_types=1);

namespace Fedir\GraphQLProjection\Commands\GeneratorWrapper\TypeResolvers;

use Fedir\GraphQLProjection\Commands\GeneratorWrapper\GeneratorTypesContext;
use Fedir\GraphQLProjection\Configs\IdPhpTypeEnum;
use Fedir\GraphQLProjection\Exceptions\UnmappedTypeException;
use GraphQL\Type\Definition\Argument;
use GraphQL\Type\Definition\FieldDefinition;
use GraphQL\Type\Definition\InputObjectField;
use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\NamedType;
use GraphQL\Type\Definition\NonNull;

class FieldDefinitionTypeResolver
{
    private string $originalType;

    private bool $nullable = true;

    private bool $list = false;

    public function __construct(
        private readonly FieldDefinition|InputObjectField|Argument $fieldDefinition,
        private readonly GeneratorTypesContext $typesContext,
    ) {
        $this->resolve();
    }

    private function resolve(): void
    {
        $type = $this->fieldDefinition->getType();

        if ($type instanceof NonNull) {
            $this->nullable = false;

            $type = $type->getWrappedType();
        }

        if ($type instanceof ListOfType) {
            $this->list = true;

            $type = $type->getWrappedType();

            if ($type instanceof NonNull) {
                $type = $type->getWrappedType();
            }
        }

        /** @var NamedType $type */
        $this->originalType = $type->name;
    }

    public function isList(): bool
    {
        return $this->list;
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }

    public function getPhpType(): string
    {
        if ($this->list) {
            return 'array';
        }

        $typeMappings = config('graphql-projection.typeMapping');

        /** @var IdPhpTypeEnum $idType */
        $idType = $typeMappings['ID'];

        //check if simple type
        $scalarType = match ($this->originalType) {
            'ID' => $idType->value,
            'String' => 'string',
            'Int' => 'int',
            'Float' => 'float',
            'Boolean' => 'bool',
            default => null,
        };

        if (! is_null($scalarType)) {
            return $scalarType;
        }

        if ($this->typesContext->isScalar($this->originalType)) {
            $scalar = $this->typesContext->getType($this->originalType);

            if (! array_key_exists($scalar->name, $typeMappings)) {
                throw new UnmappedTypeException('Scalar type '.$scalar->name.' could not be mapped to php type');
            }

            $scalarToType = $typeMappings[$scalar->name];

            if (str_contains($scalarToType, '\\')) {
                $scalarToType = '\\'.$scalarToType;
            }

            return $scalarToType;
        }

        return $this->originalType;
    }

    public function getMethodReturnType(): string
    {
        $type = $this->list
            ? 'array'
            : $this->getPhpType();

        return $this->nullable
            ? '?'.$type
            : $type;
    }

    public function getMethodDocblockType(): string
    {
        $type = $this->nullable
            ? "null|{$this->getPhpType()}"
            : $this->originalType;

        return $this->list
            ? "array<int, $type>"
            : $type;
    }

    public function getFieldName(): string
    {
        return $this->fieldDefinition->name;
    }

    public function getterName(): string
    {
        return 'get'.ucfirst($this->getFieldName());
    }

    public function setterName(): string
    {
        return 'set'.ucfirst($this->getFieldName());
    }

    public function builderSetterName(): string
    {
        return $this->getFieldName();
    }
}
