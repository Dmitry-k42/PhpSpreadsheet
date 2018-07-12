<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 06.06.18
 * Time: 16:37
 */

namespace PhpOffice\PhpSpreadsheet\Helper\XMLParser;

class FakeSimpleXMLElement implements \ArrayAccess, \Iterator
{
    private $parent;
    private $child;
    private $attributes;
    private $name;
    private $empty;
    private $value;
    private $index;
    private $iteratorKey;
    private $innerXml;

    public function __construct($parent, $name, $index = 0)
    {
        $this->parent = $parent;
        $this->name = $name;
        $this->index = $index;
        $this->child = null;
        $this->attributes = [];
        $this->empty = true;
        $this->value = null;
        $this->iteratorKey = true;
        $this->innerXml = '';
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        return $this->value = $value;
    }

    public function setEmpty($isEmpty)
    {
        $this->empty = $isEmpty;
    }

    public function isEmpty()
    {
        return $this->empty;
    }

    public function setInnerXml($str)
    {
        $this->innerXml = $str;
    }

    public function getInnerXml()
    {
        return $this->innerXml;
    }

    public function __get($name)
    {
        if ($this->child === null || $this->child->getName() != $name)
            return new FakeSimpleXMLElement($this, $name, null);
        return $this->child;
    }

    public function __isset($name)
    {
        return $this->child !== null && $this->child->getName() == $name;
    }

    public function __toString()
    {
        return $this->value !== null ? $this->value : '';
    }

    public function setChild(FakeSimpleXMLElement $el)
    {
        $this->child = $el;
    }

    public function getChild()
    {
        return $this->child;
    }

    public function removeChild()
    {
        $this->child = null;
    }

    /**
     * @return mixed
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param mixed $attributes
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    public function offsetGet($offset)
    {
        return $this->attributes[$offset] ?? null;
    }

    public function offsetSet($offset, $value)
    {
        $this->attributes[$offset] = $value;
    }

    public function offsetExists($offset)
    {
        return isset($this->attributes[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->attributes[$offset]);
    }

    public function valid()
    {
        return $this->iteratorKey && $this->index !== null;
    }

    public function rewind()
    {
        $this->iteratorKey = true;
    }

    public function next()
    {
        $this->iteratorKey = false;
        return false;
    }

    public function key()
    {
        return $this->valid() ? $this->index : null;
    }

    public function current()
    {
        return $this->valid() ? $this : null;
    }
}
