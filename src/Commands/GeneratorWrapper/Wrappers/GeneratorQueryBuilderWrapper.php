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
            . ucfirst($this->queryContainer->getName())
            . 'GraphQLQueryBuilder';
    }

    public function getStubPath(): string
    {
        return __DIR__ . '/../../stubs/build/GraphQLQueryBuilder.stub';
    }

    public function getStub(): string
    {
        $stub = file_get_contents($this->getStubPath());
        $stub = $this->replaceFieldSets($stub, $this->getArgs());

        return $this->replaceTargetClass($stub);
    }

    protected function replaceTargetClass(string $stub): string
    {
        $queryName = ucfirst($this->queryContainer->query->name) . 'GraphQLQuery';

        return str_replace(
            '{{ targetClass }}',
            $queryName,
            $stub
        );
    }
}
