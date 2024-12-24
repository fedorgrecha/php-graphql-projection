<?php

declare(strict_types=1);

use Fedir\GraphQLProjection\Configs\IdPhpTypeEnum;
use Fedir\GraphQLProjection\LighthouseSchemaProvider;
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
        'buildDir' => 'build',

        /*
         * Root Namespace for built classes
         *
         * Do not forget to add this namespace to composer.json in autoload-dev psr-4 section:
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
         *    "Fedir\\GraphQLProjection\\Build\\": "build/",
         *    ...,
         *  }
         * }
         */
        'namespace' => 'Fedir\\GraphQLProjection\\Build\\',
    ],
    'typeMapping' => [
        'ID' => IdPhpTypeEnum::INT,
        'DateTime' => Carbon::class,
        'Upload' => UploadedFile::class,
    ],
];
