<?php
namespace SoftLayer\Messaging;

class Queue extends Entity
{
    protected static $emit = array(
        'name',
        'tags',
        'visibility_interval',
        'expiration',
        'message_count',
        'visible_message_count',
    );
    protected $fetched = false;

    protected $name;
    protected $queue;
    protected $uri = '/queues';

    protected $tags = array();
    protected $visibilityInterval = 10;
    protected $expiration = 604800;
    protected $messageCount = 0;
    protected $visibleMessageCount = 0;

    public function __construct($name = '')
    {
        $this->setName($name);
    }

    public function setName($name)
    {
        $this->name = $name;
        $this->queue = $this->uri . '/' . $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }

    public function addTag($tag)
    {
        $this->tags[] = $tag;

        return $this;
    }

    public function removeTag($tag)
    {
        $index = array_search($tag, $this->tags);

        if ($index !== false) {
            array_splice($this->tags, $index, 1);
        }

        return $this;
    }

    public function getTags()
    {
        return $this->tags;
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

    public function setExpiration($expiration)
    {
        $this->expiration = $expiration;

        return $this;
    }

    public function getExpiration()
    {
        return $this->expiration;
    }

    public function getMessageCount()
    {
        if (! $this->fetched) {
            $this->fetch();
        }

        return $this->messageCount;
    }

    public function getVisibleMessageCount()
    {
        if (! $this->fetched) {
            $this->fetch();
        }

        return $this->visibleMessageCount;
    }

    public function fetch()
    {
        $this->fetched = true;

        return $this->unserialize($this->getClient()->get($this->queue)->getBody());
    }

    public function create()
    {
        return $this->save();
    }

    public function update()
    {
        return $this->save();
    }

    /** @return Queue */
    public function save()
    {
        $this->getClient()->put($this->queue, array('body' => $this->serialize()));

        return $this;
    }

    /**
     * @param bool $force
     *
     * @return Queue
     */
    public function delete($force = false)
    {
        $this->getClient()->delete($this->queue, array('params' => array('force' => $force)));

        return $this;
    }

    /**
     * @param string $body
     *
     * @return Message
     */
    public function message($body = '')
    {
        $message = new Message();
        $message->setParent($this);
        $message->setBody($body);

        return $message;
    }

    /**
     * @param int $batch
     *
     * @return Message[]
     */
    public function messages($batch = 1)
    {
        $messages = array();
        $response = $this->getClient()->get(
            sprintf('%s/messages', $this->queue),
            array('params' => array('batch' => $batch))
        );

        foreach ($response->getBody()->items as $item) {
            $message = new Message();
            $message->setParent($this);
            $message->unserialize($item);

            $messages[] = $message;
        }

        return $messages;
    }
}
