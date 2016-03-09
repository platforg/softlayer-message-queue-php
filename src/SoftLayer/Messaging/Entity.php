<?php
namespace SoftLayer\Messaging;

use SoftLayer\Http\Client;

abstract class Entity
{
    protected static $emit = array();

    protected $client;
    protected $parent;

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
        $type = explode('_', $type);

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
            // PHP can't distinguish between an empty array and an
            // empty map for JSON serialization. In every case, "fields"
            // needs to be a map - in this case represented by an empty
            // stdClass instance.
            if ($property == 'fields' && empty($this->fields)) {
                $this->fields = new \stdClass;
            }

            $payload->$property = $this->$property;
        }

        return (object) $payload;
    }

    public function unserialize($object)
    {
        foreach (static::$emit as $property) {
            if (property_exists($object, $property)) {
                $this->$property = $object->$property;
            }
        }

        return $this;
    }
}
