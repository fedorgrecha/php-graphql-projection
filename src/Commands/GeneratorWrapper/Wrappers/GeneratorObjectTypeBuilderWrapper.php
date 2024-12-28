<?php

declare(strict_types=1);

namespace GraphQLProjection\Commands\GeneratorWrapper\Wrappers;

use GraphQLProjection\Commands\GeneratorWrapper\Wrappers\Traits\GeneratorBuilderMethodTrait;

class GeneratorObjectTypeBuilderWrapper extends GeneratorObjectTypeWrapper
{
    use GeneratorBuilderMethodTrait;

    public function getClassQualifiedName(): string
    {
        return parent::getClassQualifiedName() . 'Builder';
    }

    public function getStubPath(): string
    {
        return __DIR__ . '/../../stubs/build/TypeBuilder.stub';
    }

    public function getStub(): string
    {
        $fields = $this->getFields();

        $stub = file_get_contents($this->getStubPath());
        $stub = $this->replaceFields($stub, $fields);
        $stub = $this->replaceBuilderMethods($stub, $fields);

        return $this->replaceTargetClass($stub);
    }
}
