<?php

declare(strict_types=1);

namespace GraphQLProjection\Commands\GeneratorWrapper\Wrappers;

use GraphQLProjection\Commands\GeneratorWrapper\Wrappers\Traits\GeneratorBuilderMethodTrait;

class GeneratorQueryBuilderWrapper extends GeneratorQueryWrapper
{
    use GeneratorBuilderMethodTrait;

    public function getClassQualifiedName(): string
    {
        return 'Client/'
            .ucfirst($this->queryContainer->getName())
            .'GraphQLQueryBuilder';
    }

    public function getStubPath(): string
    {
        return __DIR__.'/../../stubs/build/GraphQLQueryBuilder.stub';
    }

    public function getStub(): string
    {
        $args = $this->getArgs();
        $stub = file_get_contents($this->getStubPath());

        return $this->replaceOperationName($stub);
    }

    protected function replaceOperationName(string $stub): string
    {
        return str_replace(
            '{{ operationName }}',
            $this->queryContainer->getName(),
            $stub
        );
    }
}
