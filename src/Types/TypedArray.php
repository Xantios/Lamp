<?php

/**
 * Usage:
 * 
 * Create a class of the type you want, extend this class
 * eg:
 * 
 * class HubConfigArray extends TypedArray { 
 *    public string $type = "Xantios\Lamp\Hue\HubConfig"; // Defaults to \StdClass
 * }
 * 
 * Now you can use it as any array. except it has a explicit type
 */
namespace Xantios\Lamp\Types;

class TypedArray implements \ArrayAccess, \Countable 
{
    private array $container = [];
    public string $type = \StdClass::class;
    
    public function offsetSet($offset, $value) :void {
        if (!$value instanceof $this->type) {
            $type = get_class($value);
            throw new \Exception(sprintf('value must be an instance of StdClass not %s',$type));
        }

        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    public function offsetExists($offset) :bool {
        return isset($this->container[$offset]);
    }

    #[\ReturnTypeWillChange]
    public function offsetUnset($offset) {
        unset($this->container[$offset]);
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($offset) {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    public function count() :int {
        return count($this->container);
    }
}