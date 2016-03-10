<?php
namespace SoftLayer\Messaging\Endpoint;

use SoftLayer\Messaging\Entity;

class Queue extends Entity
{
    protected static $emit = array('queue_name');
    protected static $type = 'queue';

    public $queueName;

    public function setQueueName($queueName)
    {
        $this->queueName = $queueName;

        return $this;
    }

    public function getQueueName()
    {
        return $this->queueName;
    }
}
