<?php

declare(strict_types=1);

namespace GraphQLProjection\Configs;

enum IdPhpTypeEnum: string
{
    case STRING = 'string';
    case INT = 'int';
    case MIXED = 'int|string';
}
