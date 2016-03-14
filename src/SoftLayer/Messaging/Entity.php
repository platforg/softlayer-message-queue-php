<?php
namespace SoftLayer\Messaging;

use SoftLayer\Http\Client;

abstract class Entity
{
    protected static $emit = array();

    protected $client;
    protected $parent;
    protected $fields = array();

    public function getType()
    {
        return strtolower(get_called_class());
    }

    /**
     * @return Entity
     */
    public function getShortType()
    {
        $type = $this->getType();
        $type = explode('\\', $type);

        return array_pop($type);
    }

    /** @return Client */
    public function getClient()
    {
        return $this->getRoot()->getClient();
    }

    public function getRoot()
    {
        $parent = $this->getParent();

        if (method_exists($parent, 'getRoot')) {
            return $parent->getRoot();
        }

        return $parent;
    }

    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return Entity|Mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    public function serialize()
    {
        $payload = new \stdClass();

        foreach (static::$emit as $property) {
            if ($property == 'fields' && empty($this->fields)) {
                $this->fields = new \stdClass;
            }

            $payload->$property = $this->{$this->toCamelCase($property)};

            if ($payload->$property instanceof Entity) {
                $payload->$property = $payload->$property->serialize();
            }
        }

        return (object) $payload;
    }

    public function unserialize($object)
    {
        foreach (static::$emit as $property) {
            if (property_exists($object, $property)) {
                $this->{$this->toCamelCase($property)} = $object->$property;
            }
        }

        return $this;
    }

    protected function toCamelCase($property)
    {
        return lcfirst(join('', array_map('ucfirst', explode('_', $property))));
    }
}
