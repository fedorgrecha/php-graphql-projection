<?php

declare(strict_types=1);

namespace Fedir\GraphQLProjection\Commands\GeneratorWrapper\Wrappers;

use Fedir\GraphQLProjection\Commands\GeneratorWrapper\GeneratorTypesContext;
use Fedir\GraphQLProjection\Commands\GeneratorWrapper\GeneratorTypeWrapper;
use Fedir\GraphQLProjection\Commands\GeneratorWrapper\TypeResolvers\FieldDefinitionTypeResolver;
use Fedir\GraphQLProjection\Commands\GeneratorWrapper\Wrappers\Traits\GeneratorMethodTrait;
use GraphQL\Type\Definition\FieldDefinition;
use GraphQL\Type\Definition\InterfaceType;

class GeneratorInterfaceTypeWrapper implements GeneratorTypeWrapper
{
    use GeneratorMethodTrait;

    public function __construct(
        private readonly GeneratorTypesContext $typesContext,
        private readonly InterfaceType $type
    ) {}

    public function getClassQualifiedName(): string
    {
        return 'Types/'.$this->type->name();
    }

    public function getStubPath(): string
    {
        return __DIR__.'/../../stubs/build/Interface.stub';
    }

    public function getStub(): string
    {
        $fields = [];

        /** @var FieldDefinition $field */
        foreach ($this->type->getFields() as $field) {
            $fieldDefinition = new FieldDefinitionTypeResolver($field, $this->typesContext);

            $fields[] = $fieldDefinition;
        }

        $stub = file_get_contents($this->getStubPath());
        $stub = $this->extendInterfaces($stub);

        return $this->replaceMethods($stub, $fields, false);
    }

    private function extendInterfaces(string $stub): string
    {
        $interfaces = $this->type->getInterfaces();

        if (empty($interfaces)) {
            return $stub;
        }

        return str_replace(
            'DummyClass',
            'DummyClass extends '.implode(
                ', ',
                array_map(fn (InterfaceType $interface) => $interface->name(), $interfaces)
            ),
            $stub
        );
    }
}
