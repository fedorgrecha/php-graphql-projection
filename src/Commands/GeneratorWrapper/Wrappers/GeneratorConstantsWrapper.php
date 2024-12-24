<?php

declare(strict_types=1);

namespace Fedir\GraphQLProjection\Commands\GeneratorWrapper\Wrappers;

use Fedir\GraphQLProjection\Commands\GeneratorWrapper\GeneratorTypesContext;
use Fedir\GraphQLProjection\Commands\GeneratorWrapper\GeneratorTypeWrapper;
use GraphQL\Type\Definition\FieldDefinition;
use GraphQL\Type\Definition\ObjectType;
use Illuminate\Support\Arr;

readonly class GeneratorConstantsWrapper implements GeneratorTypeWrapper
{
    public function __construct(GeneratorTypesContext $typesContext, private ObjectType $type) {}

    public function getClassQualifiedName(): string
    {
        $c = 'Constants';

        return $c.'/'.$this->type->name().$c;
    }

    public function getStubPath(): string
    {
        return __DIR__.'/../../stubs/build/Constants.stub';
    }

    public function getStub(): string
    {
        $replace['TYPE_NAME'] = $this->type->name();

        /** @var FieldDefinition $field */
        foreach ($this->type->getFields() as $field) {
            $fieldName = $field->name;

            $replace[$fieldName] = $fieldName;
        }

        $stub = file_get_contents($this->getStubPath());

        return str_replace(
            '{{ constants }}',
            implode("\n", Arr::map($replace, fn (string $field, string $const) => "    public const $const = '$field';")),
            $stub
        );
    }
}
