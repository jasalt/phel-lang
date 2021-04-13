<?php

declare(strict_types=1);

namespace Phel\Lang\Collections\HashMap;

use ArrayAccess;
use Countable;

/**
 * @template K
 * @template V
 */
interface TransientHashMapInterface extends Countable, ArrayAccess
{
    /**
     * @param K $key
     */
    public function containsKey($key): bool;

    /**
     * @param K $key
     * @param V $value
     */
    public function put($key, $value): self;

    /**
     * @param K $key
     */
    public function remove($key): self;

    /**
     * @param K $key
     *
     * @return V
     */
    public function find($key);

    public function persistent(): PersistentHashMapInterface;
}
