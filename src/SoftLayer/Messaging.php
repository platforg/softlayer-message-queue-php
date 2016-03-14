<?php
namespace SoftLayer;

use SoftLayer\Http\Client;
use SoftLayer\Messaging\Topic;
use SoftLayer\Messaging\Queue;

class Messaging
{
    /** @var string */
    protected $endpoint;
    /** @var string */
    protected $token = null;
    /** @var Client */
    protected $client;

    public function __construct($endpoint = 'dal05', $private = false, $apiVersion = 'v1')
    {
        if (! isset(Endpoints::$endpoints[$endpoint])) {
            throw new \Exception(sprintf('Endpoint "%s" Not Found!', $endpoint));
        }

        $this->endpoint = sprintf(
            'https://%s/%s',
            Endpoints::$endpoints[$endpoint][($private ? 'private' : 'public')],
            $apiVersion
        );
    }

    public function ping()
    {
        $this->getClient()->setBaseUrl($this->endpoint);

        return $this->getClient()->get('/ping')->getBody();
    }

    public function authenticate($account, $user, $key)
    {
        $this->getClient()->setBaseUrl("{$this->endpoint}/{$account}");
        $this->getClient()->post(
            '/auth',
            array(
                'headers' => array(
                    'X-Auth-User' => $user,
                    'X-Auth-Key' => $key,
                ),
            )
        );

        $response = $this->getClient()->getResponse();

        if ($response->getStatus() == 200) {
            $this->getClient()->setDefaultHeader('X-Auth-Token', $response->getBody()->token);

            return true;
        }

        return false;
    }

    public function stats($last = 'hour')
    {
        return $this->getClient()->get('/stats/' . $last)->getBody();
    }

    /**
     * @param string $name
     *
     * @return Queue
     */
    public function queue($name = '')
    {
        $queue = new Queue();
        $queue->setParent($this);
        $queue->setName($name);

        return $queue;
    }

    /**
     * @param array $tags
     *
     * @return Queue[]
     */
    public function queues($tags = array())
    {
        $queues = array();
        $query = '/queues';

        if ($tags) {
            $query .= '?tags=' . implode(',', $tags);
        }

        $response = $this->getClient()->get($query);

        foreach ($response->getBody()->items as $item) {
            $queue = new Queue();
            $queue->setParent($this);
            $queue->unserialize($item);

            $queues[] = $queue;
        }

        return $queues;
    }

    /**
     * @param string $name
     *
     * @return Topic
     */
    public function topic($name = '')
    {
        $topic = new Topic();
        $topic->setParent($this);
        $topic->setName($name);

        return $topic;
    }

    /**
     * @param array $tags
     *
     * @return Topic[]
     */
    public function topics($tags = array())
    {
        $topics = array();
        $query = '/topics';

        if ($tags) {
            $query .= '?tags=' . implode(',', $tags);
        }

        $response = $this->getClient()->get($query);

        foreach ($response->getBody()->items as $item) {
            $topic = new Topic();
            $topic->setParent($this);
            $topic->unserialize($item);

            $topics[] = $topic;
        }

        return $topics;
    }

    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /** @return Client */
    public function getClient()
    {
        if (! $this->client) {
            $this->client = Client::getClient();
        }

        return $this->client;
    }
}
