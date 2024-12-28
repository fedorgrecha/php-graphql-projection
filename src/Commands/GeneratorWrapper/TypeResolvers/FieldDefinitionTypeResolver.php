<?php

declare(strict_types=1);

namespace GraphQLProjection\Commands\GeneratorWrapper\TypeResolvers;

use GraphQL\Type\Definition\Argument;
use GraphQL\Type\Definition\FieldDefinition;
use GraphQL\Type\Definition\InputObjectField;
use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\NamedType;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\Type;
use GraphQLProjection\Commands\GeneratorWrapper\GeneratorTypesContext;
use GraphQLProjection\Configs\IdPhpTypeEnum;
use GraphQLProjection\Exceptions\UnmappedTypeException;

class FieldDefinitionTypeResolver
{
    private string $originalType;
    private bool $nullable = true;
    private bool $list = false;
    private Type $type;
    private bool $isScalar;

    public function __construct(
        public readonly FieldDefinition|InputObjectField|Argument $fieldDefinition,
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

        $this->type = $type;

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

    public function getType(): Type
    {
        return $this->type;
    }

    /**
     * Check if type is php scalar type or simple php object like Carbon
     * Returns false if type is GraphQL type. True otherwise.
     *
     * @return bool
     */
    public function isScalar(): bool
    {
        if (empty($this->isScalar)) {
            if ($this->getIfScalar() !== null || $this->typesContext->isScalar($this->originalType)) {
                $this->isScalar = true;
            } else {
                $this->isScalar = false;
            }
        }

        return $this->isScalar;
    }

    private function getTypeMappings(): array
    {
        return config('graphql-projection.typeMapping');
    }

    private function getIfScalar(): mixed
    {
        $typeMappings = $this->getTypeMappings();

        /** @var IdPhpTypeEnum $idType */
        $idType = $typeMappings['ID'];

        return match ($this->originalType) {
            'ID' => $idType->value,
            'String' => 'string',
            'Int' => 'int',
            'Float' => 'float',
            'Boolean' => 'bool',
            default => null,
        };
    }

    public function getPhpType(): string
    {
        $typeMappings = $this->getTypeMappings();

        //check if simple type
        if (! is_null($scalarType = $this->getIfScalar())) {
            return $scalarType;
        }

        if ($this->typesContext->isScalar($this->originalType)) {
            $scalar = $this->typesContext->getType($this->originalType);

            if (! array_key_exists($scalar->name, $typeMappings)) {
                throw new UnmappedTypeException('Scalar type ' . $scalar->name . ' could not be mapped to php type');
            }

            $scalarToType = $typeMappings[$scalar->name];

            if (str_contains($scalarToType, '\\')) {
                $scalarToType = '\\' . $scalarToType;
            }

            return $scalarToType;
        }

        return $this->wrapOriginalTypeInNamespace();
    }

    private function wrapOriginalTypeInNamespace(): string
    {
        $type = $this->originalType;
        $namespace = trim(config('graphql-projection.build.namespace'), '\\');

        return '\\' . $namespace . '\\Types\\' . $type;
    }

    public function getMethodReturnType(): string
    {
        $type = $this->isList()
            ? 'array'
            : $this->getPhpType();

        return $this->isNullable()
            ? '?' . $type
            : $type;
    }

    public function getMethodDocblockType(): string
    {
        $type = $this->isNullable()
            ? "null|{$this->getPhpType()}"
            : $this->originalType;

        return $this->isList()
            ? "array<int, $type>"
            : $type;
    }

    public function getFieldName(): string
    {
        return $this->fieldDefinition->name;
    }

    public function getterName(): string
    {
        return 'get' . ucfirst($this->getFieldName());
    }

    public function setterName(): string
    {
        return 'set' . ucfirst($this->getFieldName());
    }

    public function builderSetterName(): string
    {
        return $this->getFieldName();
    }
}
