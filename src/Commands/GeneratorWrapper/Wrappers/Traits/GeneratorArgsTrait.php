<?php

declare(strict_types=1);

namespace Fedir\GraphQLProjection\Commands\GeneratorWrapper\Wrappers\Traits;

use Fedir\GraphQLProjection\Commands\GeneratorWrapper\GeneratorTypesContext;
use Fedir\GraphQLProjection\Commands\GeneratorWrapper\TypeResolvers\FieldDefinitionTypeResolver;
use Fedir\GraphQLProjection\Entities\QueryContainer;

/**
 * @property QueryContainer $queryContainer
 * @property GeneratorTypesContext $typesContext
 */
trait GeneratorArgsTrait
{
    protected function getArgs(): array
    {
        $args = [];

        foreach ($this->queryContainer->args as $arg) {
            $fieldDefinition = new FieldDefinitionTypeResolver($arg, $this->typesContext);

            $args[] = $fieldDefinition;
        }

        return $args;
    }
}
