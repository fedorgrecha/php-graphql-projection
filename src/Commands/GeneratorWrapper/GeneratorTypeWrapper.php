<?php

declare(strict_types=1);

namespace GraphQLProjection\Commands\GeneratorWrapper;

interface GeneratorTypeWrapper
{
    /**
     * @return string 'Example: SubFolder/ClassName'
     */
    public function getClassQualifiedName(): string;

    public function getStubPath(): string;

    public function getStub(): string;
}
