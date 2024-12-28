<?php

declare(strict_types=1);

namespace GraphQLProjection\Client\Projection;

/**
 * @template P
 * @template R
 */
abstract class BaseSubProjectionNode extends BaseProjection
{
    /** @var P */
    private $parent;

    /** @var R */
    private $root;

    public function __construct($parent, $root)
    {
        $this->parent = $parent;
        $this->root = $root;
    }

    /**
     * @return P
     */
    public function getParent(): mixed
    {
        return $this->parent;
    }

    /**
     * @return R
     */
    public function getRoot(): mixed
    {
        return $this->root;
    }
}
