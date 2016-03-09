<?php
namespace SoftLayer\Messaging\Endpoint;

use SoftLayer\Messaging\Entity;

class Queue extends Entity
{
    protected static $emit = array('queue_name');
    protected static $type = 'queue';

    public $queue_name;

    public function setQueueName($queue_name)
    {
        $this->queue_name = $queue_name;

        return $this;
    }

    public function getQueueName()
    {
        return $this->queue_name;
    }
}
