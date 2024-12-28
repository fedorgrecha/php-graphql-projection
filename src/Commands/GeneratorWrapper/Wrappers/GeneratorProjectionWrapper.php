<?php

declare(strict_types=1);

namespace GraphQLProjection\Commands\GeneratorWrapper\Wrappers;

use Generator;
use GraphQL\Type\Definition\FieldDefinition;
use GraphQLProjection\Commands\GeneratorWrapper\GeneratorTypesContext;
use GraphQLProjection\Commands\GeneratorWrapper\GeneratorTypeWrapper;
use GraphQLProjection\Commands\GeneratorWrapper\GeneratorTypeWrapperHasSubWrappers;
use GraphQLProjection\Commands\GeneratorWrapper\TypeResolvers\FieldDefinitionTypeResolver;
use GraphQLProjection\Entities\QueryContainer;
use Illuminate\Support\Str;

class GeneratorProjectionWrapper implements GeneratorTypeWrapper, GeneratorTypeWrapperHasSubWrappers
{
    /**
     * Returns list of subProjection data
     *
     * Sub projection data format
     * <pre>
     *     'class' => 'SubProjectionName';
     *     'root' => 'RootProjectionName',
     *     'parent' => 'ParentProjectionName',
     *     'methods' => [method content as string],
     * </pre>
     *
     * @var array
     */
    private array $subProjections = [];

    public function __construct(protected GeneratorTypesContext $typesContext, protected QueryContainer $queryContainer)
    {
    }

    public function getSubWrappers(): Generator
    {
        foreach ($this->subProjections as $subProjection) {
            yield new SubProjectionsGeneratorTypeWrapper($subProjection);
        }
    }

    public function getClassQualifiedName(): string
    {
        return 'Client/' . $this->getRootProjectionName();
    }

    public function getStubPath(): string
    {
        return __DIR__ . '/../../stubs/build/ProjectionRoot.stub';
    }

    public function getStub(): string
    {
        $stub = file_get_contents($this->getStubPath());

        return $this->generateProjectionMethods($stub);
    }

    private function getRootProjectionName(): string
    {
        return ucfirst($this->queryContainer->getName()) . 'Projection';
    }

    private function generateProjectionMethods(string $stub): string
    {
        $typeDefinition = new FieldDefinitionTypeResolver($this->queryContainer->query, $this->typesContext);
        $fields = $typeDefinition->getType()->getFields();

        $methods = [];
        $nestedProjections = [];

        $newLine = "\n";

        foreach ($fields as $field) {
            $fieldDefinition = new FieldDefinitionTypeResolver($field, $this->typesContext);

            $type = $fieldDefinition->getPhpType();
            $typeName = Str::afterLast($type, '\\');
            $nestedProjection = null;

            if (!$fieldDefinition->isScalar()) {
                $nestedProjection = ucfirst($this->queryContainer->getName()) . '_' . $typeName . 'Projection';
            }

            $methods[] = $this->generateMethod($fieldDefinition, $nestedProjection, true);

            if ($nestedProjection) {
                $nestedProjections[$nestedProjection] = $fieldDefinition;
            }
        }

        $stub = str_replace(
            '{{ methods }}',
            implode($newLine, $methods),
            $stub
        );

        $this->prepareSubProjectionsContent($nestedProjections);

        return $stub;
    }

    private function generateMethod(
        FieldDefinitionTypeResolver $fieldDefinition,
        ?string $subProjection = null,
        bool $rootProjection = false
    ): string {
        $tab = '    ';
        $newLine = "\n";

        $args = [];
        $argsString = '';

        $name = $fieldDefinition->getFieldName();

        foreach ($fieldDefinition->fieldDefinition->args as $arg) {
            $argDefinition = new FieldDefinitionTypeResolver($arg, $this->typesContext);
            $args[$argDefinition->getFieldName()] = $argDefinition->getMethodReturnType();
        }

        if ($args) {
            $argsString = implode(
                ', ',
                array_map(
                    static fn (string $argName, string $argType) => sprintf('%s $%s', $argType, $argName),
                    array_keys($args),
                    $args
                )
            );
        }

        if ($fieldDefinition->isScalar()) {
            $return = 'self';
        } else {
            $return = $subProjection;
        }

        $method = sprintf('%s%s %s(%s): %s%s', $tab, 'public function', $name, $argsString, $return, $newLine);
        $method .= "$tab{\n";

        if ($fieldDefinition->isScalar()) {
            $method .= "$tab$tab" . '$this->fields[\'' . $name . '\'] = null;' . $newLine;
        } else {
            $getRoot = $rootProjection ? '$this' : '$this->getRoot()';

            $method .= "$tab$tab" . '$projection = new ' . $subProjection . '($this, ' . $getRoot . ');' . $newLine;
            $method .= "$tab$tab" . '$this->fields[\'' . $name . '\'] = $projection;' . $newLine;
        }

        if (count($args) > 0) {
            $values = array_keys($args);
            array_walk($values, static fn (string &$value) => $value = "'$value'");
            $values = 'compact(' . implode(', ', $values) . ')';

            $method .= "$tab$tab" . '$this->inputArguments[\'' . $name . '\'] = ' . $values . ';' . $newLine;
        }

        if ($fieldDefinition->isScalar()) {
            $method .= "$newLine$tab$tab" . 'return $this;' . $newLine;
        } else {
            $method .= "$newLine$tab$tab" . 'return $projection;' . $newLine;
        }

        $method .= "$tab}\n";

        return $method;
    }

    /**
     * @param array<string, FieldDefinitionTypeResolver> $subProjections
     */
    private function prepareSubProjectionsContent(array $subProjections): void
    {
        $subProjectionClasses = [];

        foreach ($subProjections as $subProjectionName => $subProjectionFieldDefinition) {
            foreach ($subProjectionFieldDefinition->getType()->getFields() as $field) {
                $this->generateSubProjectionMethod(
                    $field,
                    $subProjectionName,
                    $this->getRootProjectionName(),
                    $subProjectionClasses
                );
            }
        }

        $mappedSubProjections = [];

        foreach ($subProjectionClasses as $subProjection => $methods) {
            $mappedSubProjections[$subProjection] = [
                'class' => 'Client/' . $subProjection,
                'root' => $methods[0]['root'],
                'parent' => $methods[0]['parent'],
                'methods' => [],
            ];

            foreach ($methods as ['method' => $method]) {
                $mappedSubProjections[$subProjection]['methods'][] = $method;
            }
        }

        $this->subProjections = array_values($mappedSubProjections);
    }

    private function generateSubProjectionMethod(
        FieldDefinition $field,
        string $currentSubProjectionName,
        string $parentProjectionName,
        array &$methodsBySubProjection = []
    ): void {
        $fieldDefinition = new FieldDefinitionTypeResolver($field, $this->typesContext);

        if ($fieldDefinition->isScalar()) {
            $methodsBySubProjection[$currentSubProjectionName][] = [
                'root' => $this->getRootProjectionName(),
                'parent' => $parentProjectionName,
                'method' => $this->generateMethod($fieldDefinition)
            ];
        } else {
            $nestedProjection = Str::replaceLast('Projection', '', $currentSubProjectionName);
            $nestedProjectionName = Str::afterLast($fieldDefinition->getPhpType(), '\\') . 'Projection';
            $nextNestedProjectionName = $nestedProjection . '_' . $nestedProjectionName;

            $methodsBySubProjection[$currentSubProjectionName][] = [
                'root' => $this->getRootProjectionName(),
                'parent' => $parentProjectionName,
                'method' => $this->generateMethod($fieldDefinition, $nextNestedProjectionName)
            ];

            foreach ($fieldDefinition->getType()->getFields() as $nestedField) {
                $this->generateSubProjectionMethod(
                    $nestedField,
                    $nextNestedProjectionName,
                    $currentSubProjectionName,
                    $methodsBySubProjection
                );
            }
        }
    }
}
