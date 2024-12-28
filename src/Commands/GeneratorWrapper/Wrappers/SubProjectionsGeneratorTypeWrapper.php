<?php

declare(strict_types=1);

namespace GraphQLProjection\Commands\GeneratorWrapper\Wrappers;

use GraphQLProjection\Commands\GeneratorWrapper\GeneratorTypeWrapper;

class SubProjectionsGeneratorTypeWrapper implements GeneratorTypeWrapper
{
    /**
     * @param array $subProjection
     */
    public function __construct(private array $subProjection)
    {
    }

    public function getClassQualifiedName(): string
    {
        return $this->subProjection['class'];
    }

    public function getStubPath(): string
    {
        return __DIR__ . '/../../stubs/build/SubProjection.stub';
    }

    public function getStub(): string
    {
        $stub = file_get_contents($this->getStubPath());

        $stub = str_replace(
            '{{ ParentNode }}',
            $this->subProjection['parent'],
            $stub
        );

        $stub = str_replace(
            '{{ RootNode }}',
            $this->subProjection['root'],
            $stub
        );

        $this->replaceMethods($stub);

        return $stub;
    }

    private function replaceMethods(string &$stub): void
    {
        $stub = str_replace(
            '{{ methods }}',
            implode("\n", $this->subProjection['methods']),
            $stub
        );
    }
}
