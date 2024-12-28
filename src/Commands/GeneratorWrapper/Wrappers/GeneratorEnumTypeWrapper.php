<?php

declare(strict_types=1);

namespace GraphQLProjection\Commands\GeneratorWrapper\Wrappers;

use GraphQL\Type\Definition\EnumType;
use GraphQLProjection\Commands\GeneratorWrapper\GeneratorTypesContext;
use GraphQLProjection\Commands\GeneratorWrapper\GeneratorTypeWrapper;
use Illuminate\Support\Arr;

readonly class GeneratorEnumTypeWrapper implements GeneratorTypeWrapper
{
    public function __construct(GeneratorTypesContext $typesContext, private EnumType $type)
    {
    }

    public function getClassQualifiedName(): string
    {
        return 'Types/' . $this->type->name();
    }

    public function getStubPath(): string
    {
        return __DIR__ . '/../../stubs/build/Enum.stub';
    }

    public function getStub(): string
    {
        $replace = [];

        foreach ($this->type->getValues() as $case) {
            $replace[] = $case->name;
        }

        $stub = file_get_contents($this->getStubPath());

        return str_replace(
            '{{ cases }}',
            implode("\n", Arr::map($replace, fn (string $case) => "    case $case;")),
            $stub
        );
    }
}
