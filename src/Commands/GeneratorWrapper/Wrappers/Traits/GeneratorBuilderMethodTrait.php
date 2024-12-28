<?php

declare(strict_types=1);

namespace GraphQLProjection\Commands\GeneratorWrapper\Wrappers\Traits;

use GraphQLProjection\Commands\GeneratorWrapper\TypeResolvers\FieldDefinitionTypeResolver;
use Illuminate\Support\Arr;

trait GeneratorBuilderMethodTrait
{
    protected function replaceBuilderMethods(string $stub, array $fields): string
    {
        return str_replace(
            '{{ builderMethods }}',
            implode("\n", Arr::map($fields, function (FieldDefinitionTypeResolver $definition) {
                $fieldsSet = 'fieldsSetFor{{ targetClass }}';
                $setterDoc = '';

                $type = $definition->getMethodReturnType();
                $name = $definition->getFieldName();

                if ($definition->isList()) {
                    $setterDoc .= "    /** @param {$definition->getMethodDocblockType()} $$name */\n";
                }

                $setter = $setterDoc . "    public function {$definition->builderSetterName()}($type $$name): self\n";

                $setter .= "    {\n";
                $setter .= '        $this->' . $name . " = $$name;" . "\n";
                $setter .= '        $this->' . $fieldsSet . "[] = '$name';" . "\n";
                $setter .= '        return $this;' . "\n";
                $setter .= "    }\n";

                return "$setter";
            })),
            $stub
        );
    }

    protected function replaceFieldSets(string $stub, array $fields): string
    {
        return str_replace(
            '{{ fieldSets }}',
            implode("\n", Arr::map($fields, function (FieldDefinitionTypeResolver $definition) {
                $fieldsSet = 'fieldsSetFor{{ targetClass }}';
                $setterDoc = '';

                $type = $definition->getMethodReturnType();
                $name = $definition->getFieldName();

                if ($definition->isList()) {
                    $setterDoc .= "    /** @param {$definition->getMethodDocblockType()} $$name */\n";
                }

                $setter = $setterDoc . "    public function {$definition->builderSetterName()}($type $$name): self\n";

                $setter .= "    {\n";
                $setter .= '        $this->' . $fieldsSet . "['$name'] = $$name;" . "\n\n";
                $setter .= '        return $this;' . "\n";
                $setter .= "    }\n";

                return "$setter";
            })),
            $stub
        );
    }

    protected function replaceTargetClass(string $stub): string
    {
        return str_replace(
            '{{ targetClass }}',
            $this->type->name(),
            $stub
        );
    }
}
