<?php
namespace SoftLayer\Messaging;

class Message extends Entity
{
    protected static $emit = array('id', 'body', 'fields', 'visibility_interval', 'visibility_delay');

    protected $id;
    protected $body;
    protected $fields = array();
    protected $visibilityInterval = 10;
    protected $visibilityDelay = 0;

    public function getId()
    {
        return $this->id;
    }

    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setFields($fields)
    {
        $this->fields = $fields;

        return $this;
    }

    public function addField($field, $value)
    {
        $this->fields[$field] = $value;

        return $this;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function setVisibilityDelay($visibilityDelay)
    {
        $this->visibilityDelay = $visibilityDelay;

        return $this;
    }

    public function getVisibilityDelay()
    {
        return $this->visibilityDelay;
    }

    public function setVisibilityInterval($visibilityInterval)
    {
        $this->visibilityInterval = $visibilityInterval;

        return $this;
    }

    public function getVisibilityInterval()
    {
        return $this->visibilityInterval;
    }

    public function create()
    {
        $this->getClient()->post(
            sprintf(
                '/%ss/%s/messages',
                $this->getParent()->getShortType(),
                $this->getParent()->getName()
            ),
            array('body' => $this->serialize())
        );

        return $this;
    }

    public function delete($id = null)
    {
        $this->getClient()->delete(
            sprintf(
                '/%ss/%s/messages/%s',
                $this->getParent()->getShortType(),
                $this->getParent()->getName(),
                $id ? $id : $this->getId()
            )
        );

        return $this;
    }
}

