<?php

declare(strict_types=1);

namespace GraphQLProjection\Commands\GeneratorWrapper\Wrappers;

use GraphQLProjection\Commands\GeneratorWrapper\GeneratorTypesContext;
use GraphQLProjection\Commands\GeneratorWrapper\GeneratorTypeWrapper;
use GraphQL\Type\Definition\UnionType;

readonly class GeneratorUnionTypeWrapper implements GeneratorTypeWrapper
{
    public function __construct(
        private GeneratorTypesContext $typesContext,
        private UnionType $type
    ) {
        $this->addUnionUsages();
    }

    private function addUnionUsages(): void
    {
        $this->typesContext->addUnionUsageIn(
            $this->type->name(),
            array_map(
                fn ($t) => $t->name(),
                $this->type->getTypes()
            )
        );
    }

    public function getClassQualifiedName(): string
    {
        return 'Types/'.$this->type->name();
    }

    public function getStubPath(): string
    {
        return __DIR__.'/../../stubs/build/Union.stub';
    }

    public function getStub(): string
    {
        return file_get_contents($this->getStubPath());
    }
}
