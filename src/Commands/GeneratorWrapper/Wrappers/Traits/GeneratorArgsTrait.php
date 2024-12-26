<?php

declare(strict_types=1);

namespace GraphQLProjection\Commands\GeneratorWrapper\Wrappers\Traits;

use GraphQLProjection\Commands\GeneratorWrapper\GeneratorTypesContext;
use GraphQLProjection\Commands\GeneratorWrapper\TypeResolvers\FieldDefinitionTypeResolver;
use GraphQLProjection\Entities\QueryContainer;

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
