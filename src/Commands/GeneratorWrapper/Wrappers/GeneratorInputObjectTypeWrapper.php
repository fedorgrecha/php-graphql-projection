<?php

declare(strict_types=1);

namespace GraphQLProjection\Commands\GeneratorWrapper\Wrappers;

use GraphQL\Type\Definition\InputObjectType;
use GraphQLProjection\Commands\GeneratorWrapper\GeneratorTypesContext;
use GraphQLProjection\Commands\GeneratorWrapper\GeneratorTypeWrapper;
use GraphQLProjection\Commands\GeneratorWrapper\Wrappers\Traits\GeneratorFieldsTrait;
use GraphQLProjection\Commands\GeneratorWrapper\Wrappers\Traits\GeneratorMethodTrait;

class GeneratorInputObjectTypeWrapper implements GeneratorTypeWrapper
{
    use GeneratorFieldsTrait;
    use GeneratorMethodTrait;

    public function __construct(
        protected GeneratorTypesContext $typesContext,
        protected InputObjectType $type
    ) {
    }

    public function getClassQualifiedName(): string
    {
        return 'Types/' . $this->type->name();
    }

    public function getStubPath(): string
    {
        return __DIR__ . '/../../stubs/build/Type.stub';
    }

    public function getStub(): string
    {
        $fields = $this->getFields();

        $stub = file_get_contents($this->getStubPath());
        $stub = $this->replaceFields($stub, $fields);

        return $this->replaceMethods($stub, $fields);
    }
}
