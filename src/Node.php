<?php
namespace ScriptFUSION\Node;

final class Node implements \Countable, \ArrayAccess, \IteratorAggregate
{
    /** @var string|int */
    private $key;

    /** @var mixed */
    private $value;

    /** @var Node|null */
    private $next;

    /** @var Node|null */
    private $previous;

    /** @var Node|null */
    private $parent;

    /** @var Node|null */
    private $firstChild;

    /** @var Node[] */
    private $children = [];

    public function __construct($key, $value)
    {
        $this->key = is_int($key) ? $key : "$key";
        $this->value = $value;
    }

    public function add(Node $node)
    {
        if ($this->has($node)) {
            throw new InvalidOperationException('Cannot add node: node already added.');
        }

        if ($lastChild = $this->getLastChild()) {
            $lastChild->next = $node;
        } else {
            $this->firstChild = $node;
        }

        $node->previous = $lastChild;
        $node->next = null;
        $node->parent = $this;

        $this->children[$node->getKey()] = $node;
    }

    public function insert(Node $before, Node $node)
    {
        if (!$this->has($before)) {
            throw new InvalidOperationException('Cannot insert node: $before not a member of this node.');
        }

        if ($previous = $before->previous) {
            $previous->next = $node;
        } else {
            $this->firstChild = $node;
        }
        $before->previous = $node;

        $node->previous = $previous;
        $node->next = $before;
        $node->parent = $this;

        array_unshift($this->children, $node);
    }

    public function remove(Node $node)
    {
        if (!$this->has($node)) {
            throw new InvalidOperationException('Cannot remove node: not a member of this node.');
        }

        if ($next = $node->next) {
            $next->previous = $node->previous;
        }
        if ($previous = $node->previous) {
            $previous->next = $next;
        } else {
            $this->firstChild = $next;
        }

        $node->previous = $node->next = $node->parent = null;

        unset($this->children[$node->getKey()]);
    }

    public function has(Node $node)
    {
        return isset($this->children[$node->getKey()]);
    }

    #region Countable

    public function count()
    {
        return count($this->children);
    }

    #endregion

    #region ArrayAccess

    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    public function offsetGet($offset)
    {
        return $this->children[$offset->getKey()];
    }

    public function offsetSet($offset, $value)
    {
        $this->add($value);
    }

    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    #endregion

    #region IteratorAggregate

    public function getIterator()
    {
        if ($node = $this->firstChild) {
            do {
                yield $node;
            } while ($node = $node->next);
        }
    }

    #endregion

    /**
     * @return int|string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return Node|null
     */
    public function getNext()
    {
        return $this->next;
    }

    /**
     * @return Node|null
     */
    public function getPrevious()
    {
        return $this->previous;
    }

    /**
     * @return Node|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return Node|null
     */
    public function getFirstChild()
    {
        return $this->firstChild;
    }

    /**
     * @return Node|null
     */
    public function getLastChild()
    {
        if ($lastChild = end($this->children)) {
            return $lastChild;
        }
    }
}
