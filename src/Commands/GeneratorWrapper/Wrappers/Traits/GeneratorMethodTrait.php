<?php

declare(strict_types=1);

namespace GraphQLProjection\Commands\GeneratorWrapper\Wrappers\Traits;

use GraphQLProjection\Commands\GeneratorWrapper\TypeResolvers\FieldDefinitionTypeResolver;
use Illuminate\Support\Arr;

trait GeneratorMethodTrait
{
    /**
     * @param  array<int, FieldDefinitionTypeResolver>  $fields
     */
    protected function replaceMethods(string $stub, array $fields, bool $withBody = true): string
    {
        return str_replace(
            '{{ methods }}',
            implode("\n", Arr::map($fields, function (FieldDefinitionTypeResolver $definition) use ($withBody) {
                $getterDoc = '';
                $setterDoc = '';

                $type = $definition->getMethodReturnType();
                $name = $definition->getFieldName();

                if ($definition->isList()) {
                    $getterDoc .= "    /** @return {$definition->getMethodDocblockType()} */\n";
                    $setterDoc .= "    /** @param {$definition->getMethodDocblockType()} $$name */\n";
                }

                $getter = $getterDoc . "    public function {$definition->getterName()}(): $type"
                    . ($withBody ? "\n" : ';');

                if ($withBody) {
                    $getter .= "    {\n";
                    $getter .= '        return $this->' . $name . ";\n";
                    $getter .= "    }\n";
                }

                $setter = $setterDoc . "    public function {$definition->setterName()}($type $$name): void"
                    . ($withBody ? "\n" : ';');

                if ($withBody) {
                    $setter .= "    {\n";
                    $setter .= '        $this->' . $name . " = $$name;" . "\n";
                    $setter .= "    }\n";
                }

                return "$getter\n$setter";
            })),
            $stub
        );
    }
}
