<?php
namespace App\Services\EbayService\Types;

use App\Services\EbayService\Exceptions\InvalidPropertyTypeException;


/**
 * Class to handle XML elements that are repeatable.
 *
 * Allows properties in an object to be treated as an array.
 */
class RepeatableType implements \ArrayAccess, \Countable, \Iterator
{
    private $data = [];
    private $position = 0;
    private $class;
    private $property;
    private $expectedType;

    public function __construct($class, $property, $expectedType)
    {
        $this->class = $class;
        $this->property = $property;
        $this->expectedType = $expectedType;
    }

    public function offsetExists($offset):bool
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->data[$offset] : null;
    }

    public function offsetSet($offset, $value):void
    {
        $this->ensurePropertyType($value);

        if (is_null($offset)) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    public function offsetUnset($offset):void
    {
        unset($this->data[$offset]);
    }

    public function count():int
    {
        return count($this->data);
    }

    public function current()
    {
        return $this->offsetGet($this->position);
    }

    public function key()
    {
        return $this->position;
    }

    public function next():void
    {
        $this->position++;
    }

    public function rewind():void
    {
        $this->position = 0;
    }

    public function valid():bool
    {
        return $this->offsetExists($this->position);
    }

    private function ensurePropertyType($value)
    {
        $actualType = is_object($value) ? get_class($value) : gettype($value);
        $validTypes = explode('|', $this->expectedType);

        $isValid = false;
        foreach ($validTypes as $check) {
            if ($check === 'any' || $check === $actualType) {
                $isValid = true;
                break;
            }
        }

        if (!$isValid) {
            throw new InvalidPropertyTypeException($this->property, $this->expectedType, $actualType);
        }
    }
}
