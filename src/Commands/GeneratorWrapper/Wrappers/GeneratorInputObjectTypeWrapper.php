<?php

declare(strict_types=1);

namespace Fedir\GraphQLProjection\Commands\GeneratorWrapper\Wrappers;

use Fedir\GraphQLProjection\Commands\GeneratorWrapper\GeneratorTypesContext;
use Fedir\GraphQLProjection\Commands\GeneratorWrapper\GeneratorTypeWrapper;
use Fedir\GraphQLProjection\Commands\GeneratorWrapper\Wrappers\Traits\GeneratorFieldsTrait;
use Fedir\GraphQLProjection\Commands\GeneratorWrapper\Wrappers\Traits\GeneratorMethodTrait;
use GraphQL\Type\Definition\InputObjectType;

class GeneratorInputObjectTypeWrapper implements GeneratorTypeWrapper
{
    use GeneratorFieldsTrait;
    use GeneratorMethodTrait;

    public function __construct(
        protected GeneratorTypesContext $typesContext,
        protected InputObjectType $type
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
        $stub = $this->replaceFields($stub, $fields);

        return $this->replaceMethods($stub, $fields);
    }
}
