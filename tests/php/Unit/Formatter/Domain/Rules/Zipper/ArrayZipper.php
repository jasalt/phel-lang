<?php

declare(strict_types=1);

namespace PhelTest\Unit\Formatter\Domain\Rules\Zipper;

use Phel\Formatter\Domain\Rules\Zipper\AbstractZipper;
use Phel\Formatter\Domain\Rules\Zipper\ZipperException;

use function is_array;

/**
 * @extends AbstractZipper<list<int>>
 */
final class ArrayZipper extends AbstractZipper
{
    /**
     * @param list<int|list<int>> $root
     */
    public static function createRoot(array $root): self
    {
        return new self($root, null, [], [], false, false);
    }

    public function isBranch(): bool
    {
        return is_array($this->node);
    }

    /**
     * @throws ZipperException
     *
     * @return list<int>
     */
    public function getChildren(): array
    {
        if (!$this->isBranch()) {
            throw ZipperException::calledChildrenOnLeafNode();
        }

        return $this->node;
    }

    /**
     * @param list<int>       $node
     * @param list<list<int>> $children
     *
     * @return list<int>
     */
    public function makeNode(mixed $node, array $children): array
    {
        return $children;
    }

    /**
     * @param list<int|list<int>>        $node
     * @param ?AbstractZipper<list<int>> $parent
     * @param list<int|list<int>>        $leftSiblings
     * @param list<int|list<int>>        $rightSiblings
     */
    protected function createNewInstance(
        $node,
        ?AbstractZipper $parent,
        array $leftSiblings,
        array $rightSiblings,
        bool $hasChanged,
        bool $isEnd,
    ): static {
        return new self($node, $parent, $leftSiblings, $rightSiblings, $hasChanged, $isEnd);
    }
}
