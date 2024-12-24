<?php

declare(strict_types=1);

namespace Fedir\GraphQLProjection\Commands\GeneratorWrapper;

use Exception;

interface GeneratorTypeWrapperContainerInterface
{
    public function shouldGenerate(): bool;

    /**
     * @return array<GeneratorTypeWrapper>
     *
     * @throws Exception
     */
    public function getWrappers(): array;
}
