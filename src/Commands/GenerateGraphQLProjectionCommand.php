<?php

namespace GraphQLProjection\Commands;

use Exception;
use Generator;
use GraphQL\Type\Definition\NamedType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQLProjection\Commands\GeneratorWrapper\EntityGenerator\GeneratorTypeWrapperFactory;
use GraphQLProjection\Commands\GeneratorWrapper\GeneratorTypesContext;
use GraphQLProjection\Commands\GeneratorWrapper\GeneratorTypeWrapper;
use GraphQLProjection\Commands\GeneratorWrapper\GeneratorTypeWrapperContainerInterface;
use GraphQLProjection\Commands\GeneratorWrapper\GeneratorTypeWrapperHasSubWrappers;
use GraphQLProjection\Entities\QueryContainer;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use RuntimeException;

class GenerateGraphQLProjectionCommand extends GeneratorCommand
{
    protected $signature = 'graphql:projection';
    protected $description = 'Generate GraphQL projection';
    private readonly GraphQLSchemaProvider $schemaProvider;

    public function __construct(
        Filesystem $files,
        private readonly GeneratorTypeWrapperFactory $generatorFactory,
    ) {
        parent::__construct($files);

        $this->schemaProvider = resolve(config('graphql-projection.schema.provider'));
    }

    /** @throws Exception */
    public function handle(): ?bool
    {
        /** @var float $start */
        $start = microtime(true);

        $this->prepare();
        $this->schemaProvider->beforeGeneration();

        $this->generateAll($this->schemaProvider->getSchema());

        $this->schemaProvider->afterGeneration();

        /** @var float $finish */
        $finish = microtime(true);

        $seconds = $finish - $start;
        $this->info('Finished in ' . round($seconds, 2) . ' seconds');

        return true;
    }

    private function prepare(): void
    {
        $this->files->deleteDirectory($this->generatorFactory->generatedRootPath());

        foreach ($this->generatorFactory->getBuildDirectories() as $directory => $mode) {
            $this->files->makeDirectory($directory, $mode, true);
        }
    }

    /**
     * @throws Exception
     */
    private function generateAll(Schema $schema): void
    {
        /** @var array<string, Type&NamedType> $types */
        $types = $schema->getTypeMap();

        $context = new GeneratorTypesContext($types);

        //Generate types that must be present while generating other types
        //Such as: interfaces, union (interface usage), constants, enums
        /** @var Type&NamedType $type */
        foreach ($types as $type) {
            $this->build(
                $this->generatorFactory->getFirstPriorityWrapperContainers($context, $type)
            );
        }

        /** @var Type&NamedType $type */
        foreach ($types as $type) {
            $this->build(
                $this->generatorFactory->getWrapperContainer($context, $type)
            );
        }

        foreach ($this->getQueriesAndMutations($schema) as $query) {
            if (is_null($query)) {
                break;
            }

            $this->build(
                $this->generatorFactory->getQueryWrapperContainer($context, $query)
            );
        }
    }

    /** @throws Exception */
    private function build(GeneratorTypeWrapperContainerInterface $container): void
    {
        if (! $container->shouldGenerate()) {
            return;
        }

        foreach ($container->getWrappers() as $wrapper) {
            $this->buildWrapper($wrapper);

            if ($wrapper instanceof GeneratorTypeWrapperHasSubWrappers) {
                foreach ($wrapper->getSubWrappers() as $subWrapper) {
                    $this->buildWrapper($subWrapper);
                }
            }
        }
    }

    private function buildWrapper(GeneratorTypeWrapper $wrapper): void
    {
        $class = $this->qualifyClass($wrapper->getClassQualifiedName());
        $path = $this->getPath($class);
        $stub = $wrapper->getStub();

        $this->files->put(
            $path,
            $this->replaceNamespace($stub, $class)
                ->replaceClass($stub, $class)
        );
    }

    /** @return null|Generator<QueryContainer> */
    private function getQueriesAndMutations(Schema $schema): ?Generator
    {
        foreach ($schema->getQueryType()?->getFields() ?? [] as $field) {
            yield new QueryContainer(false, $field);
        }

        foreach ($schema->getMutationType()?->getFields() ?? [] as $field) {
            yield new QueryContainer(true, $field);
        }

        yield null;
    }

    protected function getPath($name): string
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return base_path($this->generatorFactory->getBuildRootDir()) . str_replace('\\', '/', $name) . '.php';
    }

    protected function rootNamespace(): string
    {
        return $this->generatorFactory->getBuildNamespace();
    }

    protected function getStub(): string
    {
        throw new RuntimeException('Stubs must be generated by GeneratorTypeWrapperContainerInterface');
    }
}
