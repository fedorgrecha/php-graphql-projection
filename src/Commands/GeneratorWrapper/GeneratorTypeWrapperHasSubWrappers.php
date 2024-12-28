<?php

declare(strict_types=1);

namespace GraphQLProjection\Commands\GeneratorWrapper;

use Generator;

interface GeneratorTypeWrapperHasSubWrappers
{
    public function getSubWrappers(): Generator;
}
