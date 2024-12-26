<?php

declare(strict_types=1);

use GraphQLProjection\Configs\IdPhpTypeEnum;
use GraphQLProjection\LighthouseSchemaProvider;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;

return [
    'schema' => [
        /*
         * Get graphql schema provider.
         *
         * Must implements GraphQLSchemaProvider interface
         */
        'provider' => LighthouseSchemaProvider::class,
    ],
    'build' => [
        /*
         * Build root.
         *
         * Get by base_path()
         */
        'buildDir' => 'build/graphql-projection/',

        /*
         * Root Namespace for built classes
         *
         * Don't forget to add this namespace to composer.json in autoload-dev psr-4 section:
         * "autoload-dev": {
         *  "psr-4": {
         *    "{namespace}": "{buildDir}/",
         *  }
         * }
         *
         * Example:
         * "autoload-dev": {
         *  "psr-4": {
         *    ...,
         *    "Build\\GraphQLProjection\\": "build/graphql-projection/",
         *    ...,
         *  }
         * }
         */
        'namespace' => 'Build\\GraphQLProjection\\',
    ],
    'typeMapping' => [
        'ID' => IdPhpTypeEnum::INT,
        'DateTime' => Carbon::class,
        'Upload' => UploadedFile::class,
    ],
];
