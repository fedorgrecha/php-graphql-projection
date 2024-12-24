<?php

declare(strict_types=1);

namespace Fedir\GraphQLProjection\Commands\GeneratorWrapper;

interface GeneratorTypeWrapper
{
    /**
     * @return string 'Example: SubFolder/ClassName'
     */
    public function getClassQualifiedName(): string;

    public function getStubPath(): string;

    public function getStub(): string;
}
