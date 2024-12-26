<?php

declare(strict_types=1);

namespace GraphQLProjection\Commands\GeneratorWrapper\Wrappers\Traits;

use GraphQLProjection\Commands\GeneratorWrapper\GeneratorTypesContext;
use GraphQLProjection\Commands\GeneratorWrapper\TypeResolvers\FieldDefinitionTypeResolver;
use GraphQL\Type\Definition\FieldDefinition;
use GraphQL\Type\Definition\InputObjectField;
use GraphQL\Type\Definition\NamedType;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Arr;

/**
 * @property NamedType&Type $type
 * @property GeneratorTypesContext $typesContext
 */
trait GeneratorFieldsTrait
{
    /**
     * @return array<int, FieldDefinitionTypeResolver>
     */
    protected function getFields(): array
    {
        $fields = [];

        /** @var FieldDefinition|InputObjectField $field */
        foreach ($this->type->getFields() as $field) {
            $fieldDefinition = new FieldDefinitionTypeResolver($field, $this->typesContext);

            $fields[] = $fieldDefinition;
        }

        return $fields;
    }

    /**
     * @param  array<int, FieldDefinitionTypeResolver>  $fields
     */
    protected function replaceFields(string $stub, array $fields): string
    {
        return str_replace(
            '{{ fields }}',
            implode("\n", Arr::map($fields, function (FieldDefinitionTypeResolver $definition) {
                $doc = '';

                $type = $definition->getMethodReturnType();
                $name = $definition->getFieldName();

                if ($definition->isList()) {
                    $doc .= "    /** @var {$definition->getMethodDocblockType()} */\n";
                }

                return $doc."    private $type $$name;";
            })),
            $stub
        );
    }
}
