<?php
namespace SoftLayer\Messaging;

use SoftLayer\Messaging;

class Topic extends Entity
{
    protected static $emit = array('name', 'tags');

    protected $name;
    protected $topic;
    protected $uri = '/topics';
    protected $tags = array();

    public function __construct($name = '')
    {
        $this->setName($name);
    }

    public function setName($name)
    {
        $this->name = $name;
        $this->topic = $this->uri . '/' . $name;

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

    public function fetch()
    {
        return $this->unserialize($this->getClient()->get($this->topic)->getBody());
    }

    public function create()
    {
        return $this->save();
    }

    public function update()
    {
        return $this->save();
    }

    public function save()
    {
        $this->getClient()->put($this->topic, array('body' => $this->serialize()));

        return $this;
    }

    public function delete($force = false)
    {
        $this->getClient()->delete($this->topic, array('params' => array('force' => $force)));

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
     * @param string $endpointType
     *
     * @return Subscription
     */
    public function subscription($endpointType = '')
    {
        $subscription = new Subscription();
        $subscription->setParent($this);
        $subscription->setEndpointType($endpointType);

        return $subscription;
    }

    /** @return Subscription[] */
    public function subscriptions()
    {
        $subscriptions = array();
        $response = $this->getClient()->get(sprintf('%s/subscriptions', $this->topic));

        foreach ($response->getBody()->items as $item) {
            $subscription = new Subscription();
            $subscription->setParent($this);
            $subscription->unserialize($item);

            $endpoint = Endpoint::endpointByType($subscription->getEndpointType());
            $endpoint->setParent($subscription);
            $endpoint->unserialize($subscription->getEndpoint());

            $subscription->setEndpoint($endpoint);

            $subscriptions[] = $subscription;
        }

        return $subscriptions;
    }
}
