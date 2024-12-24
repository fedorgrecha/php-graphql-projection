<?php

declare(strict_types=1);

namespace Fedir\GraphQLProjection\Commands\GeneratorWrapper;

use Exception;
use Fedir\GraphQLProjection\Commands\GeneratorWrapper\Wrappers\GeneratorQueryBuilderWrapper;
use Fedir\GraphQLProjection\Commands\GeneratorWrapper\Wrappers\GeneratorQueryWrapper;
use Fedir\GraphQLProjection\Entities\QueryContainer;

readonly class GeneratorQueryWrapperContainer implements GeneratorTypeWrapperContainerInterface
{
    public function __construct(protected GeneratorTypesContext $typesContext, protected QueryContainer $query) {}

    public function shouldGenerate(): bool
    {
        return true;
    }

    /**
     * @return array<GeneratorTypeWrapper>
     *
     * @throws Exception
     */
    public function getWrappers(): array
    {
        return [
            //new GeneratorQueryBuilderWrapper($this->typesContext, $this->query),
            new GeneratorQueryWrapper($this->typesContext, $this->query),
        ];
    }
}
