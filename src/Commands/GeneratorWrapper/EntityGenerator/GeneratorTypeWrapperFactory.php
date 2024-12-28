<?php

declare(strict_types=1);

namespace GraphQLProjection\Commands\GeneratorWrapper\EntityGenerator;

use GraphQL\Type\Definition\NamedType;
use GraphQL\Type\Definition\Type;
use GraphQLProjection\Commands\GeneratorWrapper\GeneratorTypesContext;
use GraphQLProjection\Commands\GeneratorWrapper\GeneratorTypeWrapperContainerInterface;
use GraphQLProjection\Entities\QueryContainer;

class GeneratorTypeWrapperFactory
{
    private const DIR_MODE = 0777;

    public function getBuildRootDir(): string
    {
        return trim(config('graphql-projection.build.buildDir'), '/') . '/';
    }

    public function getBuildTypeDir(): string
    {
        return 'Types';
    }

    public function getBuildClientDir(): string
    {
        return 'Client';
    }

    public function getBuildConstantsDir(): string
    {
        return 'Constants';
    }

    public function getBuildNamespace(): string
    {
        return config('graphql-projection.build.namespace');
    }

    public function generatedRootPath(?string $path = null): string
    {
        return base_path($this->getBuildRootDir() . $path);
    }

    /**
     * string - Directory path
     * int - Directory mode
     *
     * @return array<string, int>
     */
    public function getBuildDirectories(): array
    {
        return [
            $this->generatedRootPath() => self::DIR_MODE,
            $this->generatedRootPath($this->getBuildTypeDir()) => self::DIR_MODE,
            $this->generatedRootPath($this->getBuildClientDir()) => self::DIR_MODE,
            $this->generatedRootPath($this->getBuildConstantsDir()) => self::DIR_MODE,
        ];
    }

    public function getFirstPriorityWrapperContainers(
        GeneratorTypesContext $typesContext,
        Type&NamedType $type
    ): GeneratorTypeWrapperContainerInterface {
        return new GeneratorFirstPriorityTypeWrapperContainer($typesContext, $type);
    }

    public function getWrapperContainer(
        GeneratorTypesContext $typesContext,
        Type&NamedType $type
    ): GeneratorTypeWrapperContainerInterface {
        return new GeneratorTypeWrapperContainer($typesContext, $type);
    }

    public function getQueryWrapperContainer(
        GeneratorTypesContext $typesContext,
        QueryContainer $query
    ): GeneratorTypeWrapperContainerInterface {
        return new GeneratorQueryWrapperContainer($typesContext, $query);
    }
}
