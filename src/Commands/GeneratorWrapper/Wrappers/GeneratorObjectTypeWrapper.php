<?php

declare(strict_types=1);

namespace GraphQLProjection\Commands\GeneratorWrapper\Wrappers;

use GraphQLProjection\Commands\GeneratorWrapper\GeneratorTypesContext;
use GraphQLProjection\Commands\GeneratorWrapper\GeneratorTypeWrapper;
use GraphQLProjection\Commands\GeneratorWrapper\Wrappers\Traits\GeneratorFieldsTrait;
use GraphQLProjection\Commands\GeneratorWrapper\Wrappers\Traits\GeneratorMethodTrait;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\ObjectType;

class GeneratorObjectTypeWrapper implements GeneratorTypeWrapper
{
    use GeneratorFieldsTrait;
    use GeneratorMethodTrait;

    public function __construct(
        protected readonly GeneratorTypesContext $typesContext,
        protected readonly ObjectType $type
    ) {}

    public function getClassQualifiedName(): string
    {
        return 'Types/'.$this->type->name();
    }

    public function getStubPath(): string
    {
        return __DIR__.'/../../stubs/build/Type.stub';
    }

    public function getStub(): string
    {
        $fields = $this->getFields();

        $stub = file_get_contents($this->getStubPath());
        $stub = $this->implementInterfaces($stub);
        $stub = $this->replaceFields($stub, $fields);

        return $this->replaceMethods($stub, $fields);
    }

    protected function implementInterfaces(string $stub): string
    {
        $unions = $this->typesContext->getUnionUsageIn($this->type->name);
        $interfaces = $this->type->getInterfaces();

        $allInterfaces = array_merge(
            $unions,
            array_map(fn (InterfaceType $interface) => $interface->name(), $interfaces)
        );

        if (empty($allInterfaces)) {
            return $stub;
        }

        return str_replace(
            'DummyClass',
            'DummyClass implements '.implode(
                ', ',
                array_unique($allInterfaces)
            ),
            $stub
        );
    }
}
