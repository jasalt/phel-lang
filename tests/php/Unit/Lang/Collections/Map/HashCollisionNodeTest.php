<?php

declare(strict_types=1);

namespace PhelTest\Unit\Lang\Collections\Map;

use Phel\Lang\Collections\Map\Box;
use Phel\Lang\Collections\Map\HashCollisionNode;
use Phel\Lang\Collections\Map\HashMapNodeInterface;
use Phel\Lang\Collections\Map\IndexedNode;
use PhelTest\Unit\Lang\Collections\ModuloHasher;
use PhelTest\Unit\Lang\Collections\SimpleEqualizer;
use PHPUnit\Framework\TestCase;

final class HashCollisionNodeTest extends TestCase
{
    public function test_find_on_single_collision_node(): void
    {
        $hasher = new ModuloHasher();
        $node = new HashCollisionNode($hasher, new SimpleEqualizer(), $hasher->hash(1), 1, [1, 'test']);

        $this->assertSame('test', $node->find(0, $hasher->hash(1), 1, null));
        $this->assertNull($node->find(0, $hasher->hash(2), 2, null));
    }

    public function test_find_with_multiple_entries(): void
    {
        $hasher = new ModuloHasher(2);
        $node = new HashCollisionNode(
            $hasher,
            new SimpleEqualizer(),
            $hasher->hash(1),
            2,
            [1, 'foo', 3, 'bar'],
        );

        $this->assertSame('foo', $node->find(0, $hasher->hash(1), 1, null));
        $this->assertSame('bar', $node->find(0, $hasher->hash(3), 3, null));
        $this->assertNull($node->find(0, $hasher->hash(2), 2, null));
    }

    public function test_put_another_key_with_same_hash(): void
    {
        $hasher = new ModuloHasher(2);
        $box = new Box(null);
        $node = (new HashCollisionNode($hasher, new SimpleEqualizer(), $hasher->hash(1), 1, [1, 'foo']))
            ->put(0, $hasher->hash(3), 3, 'bar', $box);

        $this->assertTrue($box->getValue());
        $this->assertSame('foo', $node->find(0, $hasher->hash(1), 1, null));
        $this->assertSame('bar', $node->find(0, $hasher->hash(3), 3, null));
        $this->assertNull($node->find(0, $hasher->hash(2), 2, null));
    }

    public function test_update_existing_key(): void
    {
        $hasher = new ModuloHasher(2);
        $box = new Box(null);
        $node = (new HashCollisionNode($hasher, new SimpleEqualizer(), $hasher->hash(1), 1, [1, 'foo']))
            ->put(0, $hasher->hash(1), 1, 'bar', $box);

        $this->assertNull($box->getValue());
        $this->assertSame('bar', $node->find(0, $hasher->hash(1), 1, null));
        $this->assertNull($node->find(0, $hasher->hash(2), 2, null));
    }

    public function test_update_existing_key_with_same_value(): void
    {
        $hasher = new ModuloHasher(2);
        $box = new Box(false);
        $node = (new HashCollisionNode($hasher, new SimpleEqualizer(), $hasher->hash(1), 1, [1, 'foo']))
            ->put(0, $hasher->hash(1), 1, 'foo', $box);

        $this->assertFalse($box->getValue());
        $this->assertSame('foo', $node->find(0, $hasher->hash(1), 1, null));
        $this->assertNull($node->find(0, $hasher->hash(2), 2, null));
    }

    public function test_put_another_hash(): void
    {
        $hasher = new ModuloHasher(2);
        $box = new Box(null);
        $node = (new HashCollisionNode($hasher, new SimpleEqualizer(), $hasher->hash(1), 1, [1, 'foo']))
            ->put(0, $hasher->hash(2), 2, 'bar', $box);

        $this->assertTrue($box->getValue());
        $this->assertInstanceOf(IndexedNode::class, $node);
        $this->assertSame('foo', $node->find(0, $hasher->hash(1), 1, null));
        $this->assertSame('bar', $node->find(0, $hasher->hash(2), 2, null));
        $this->assertNull($node->find(0, $hasher->hash(3), 3, null));
    }

    public function test_remove_only_inserted_key(): void
    {
        $hasher = new ModuloHasher(2);
        $node = (new HashCollisionNode($hasher, new SimpleEqualizer(), $hasher->hash(1), 1, [1, 'foo']))
            ->remove(0, $hasher->hash(1), 1);

        $this->assertNotInstanceOf(HashMapNodeInterface::class, $node);
    }

    public function test_remove_non_existing_key(): void
    {
        $hasher = new ModuloHasher(2);
        $node = (new HashCollisionNode($hasher, new SimpleEqualizer(), $hasher->hash(1), 1, [1, 'foo']))
            ->remove(0, $hasher->hash(2), 2);
        $this->assertInstanceOf(HashMapNodeInterface::class, $node);

        $this->assertSame('foo', $node->find(0, $hasher->hash(1), 1, null));
        $this->assertNull($node->find(0, $hasher->hash(2), 2, null));
    }

    public function test_remove_one_collision_key(): void
    {
        $hasher = new ModuloHasher(2);
        $node = (new HashCollisionNode(
            $hasher,
            new SimpleEqualizer(),
            $hasher->hash(1),
            2,
            [1, 'foo', 3, 'bar'],
        ))->remove(0, $hasher->hash(3), 3);
        $this->assertInstanceOf(HashMapNodeInterface::class, $node);

        $this->assertSame('foo', $node->find(0, $hasher->hash(1), 1, null));
        $this->assertNull($node->find(0, $hasher->hash(3), 3, null));
    }
}
