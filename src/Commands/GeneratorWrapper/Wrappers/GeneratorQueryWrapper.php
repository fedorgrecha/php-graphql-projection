<?php

declare(strict_types=1);

namespace GraphQLProjection\Commands\GeneratorWrapper\Wrappers;

use GraphQLProjection\Commands\GeneratorWrapper\GeneratorTypesContext;
use GraphQLProjection\Commands\GeneratorWrapper\GeneratorTypeWrapper;
use GraphQLProjection\Commands\GeneratorWrapper\Wrappers\Traits\GeneratorArgsTrait;
use GraphQLProjection\Commands\GeneratorWrapper\Wrappers\Traits\GeneratorBuilderMethodTrait;
use GraphQLProjection\Entities\QueryContainer;

class GeneratorQueryWrapper implements GeneratorTypeWrapper
{
    use GeneratorArgsTrait;
    use GeneratorBuilderMethodTrait;

    public function __construct(protected GeneratorTypesContext $typesContext, protected QueryContainer $queryContainer)
    {
    }

    public function getClassQualifiedName(): string
    {
        return 'Client/'
            . ucfirst($this->queryContainer->getName())
            . 'GraphQLQuery';
    }

    public function getStubPath(): string
    {
        return __DIR__ . '/../../stubs/build/GraphQLQuery.stub';
    }

    public function getStub(): string
    {
        $stub = file_get_contents($this->getStubPath());
        $stub = $this->replaceOperationName($stub);

        return $this->replaceOperation($stub);
    }

    protected function replaceOperationName(string $stub): string
    {
        return str_replace(
            '{{ operationName }}',
            $this->queryContainer->getName(),
            $stub
        );
    }

    protected function replaceOperation(string $stub): string
    {
        return str_replace(
            '{{ operation }}',
            $this->queryContainer->isMutation ? 'mutation' : 'query',
            $stub
        );
    }
}
