<?php
namespace ScriptFUSIONTest\Unit\Node;

use ScriptFUSION\Node\InvalidOperationException;
use ScriptFUSION\Node\Node;

final class NodeTest extends \PHPUnit_Framework_TestCase
{
    /** @var Node */
    private $node;

    protected function setUp()
    {
        $this->node = new Node('foo', 'bar');
    }

    public function testInitialState()
    {
        self::assertSame('foo', $this->node->getKey());
        self::assertSame('bar', $this->node->getValue());
        self::assertNull($this->node->getNext());
        self::assertNull($this->node->getPrevious());
        self::assertNull($this->node->getParent());
        self::assertNull($this->node->getFirstChild());
        self::assertNull($this->node->getLastChild());
        self::assertFalse($this->node->getIterator()->valid());
        self::assertCount(0, $this->node);
    }

    public function testAdd()
    {
        $this->node->add($child1 = $this->createUniqueNode());

        // Examine parent.
        self::assertNull($this->node->getNext());
        self::assertNull($this->node->getPrevious());
        self::assertNull($this->node->getParent());
        self::assertSame($child1, $this->node->getFirstChild());
        self::assertSame($child1, $this->node->getLastChild());
        self::assertSame($child1, $this->node->getIterator()->current());
        self::assertCount(1, $this->node);

        // Examine child1.
        self::assertNull($child1->getPrevious());
        self::assertNull($child1->getNext());
        self::assertSame($this->node, $child1->getParent());
        self::assertCount(0, $child1);

        $this->node->add($child2 = $this->createUniqueNode());

        // Examine parent.
        self::assertNull($this->node->getNext());
        self::assertNull($this->node->getPrevious());
        self::assertNull($this->node->getParent());
        self::assertSame($child1, $this->node->getFirstChild());
        self::assertSame($child2, $this->node->getLastChild());
        self::assertSame($child1, $this->node->getIterator()->current());
        self::assertCount(2, $this->node);

        // Examine child1.
        self::assertNull($child1->getPrevious());
        self::assertSame($child2, $child1->getNext());
        self::assertSame($this->node, $child1->getParent());
        self::assertCount(0, $child1);

        // Examine child2.
        self::assertSame($child1, $child2->getPrevious());
        self::assertNull($child2->getNext());
        self::assertSame($this->node, $child1->getParent());
        self::assertCount(0, $child1);
    }

    public function testAddDuplicateNode()
    {
        $this->setExpectedException(InvalidOperationException::class);

        $this->node->add($node = $this->createUniqueNode());
        $this->node->add($node);
    }

    public function testInsert()
    {

    }

    public function testRemove()
    {

    }

    private function createUniqueNode()
    {
        return new Node($id = uniqid('', true), $id);
    }
}
