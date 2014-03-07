<?php

require_once 'bootstrap.php';

class TopicTest extends BaseTest
{
    public function testTopicsList()
    {
        $topicName = self::topicName();

        self::$messaging->topic($topicName)->create();

        sleep(WAIT);

        $topics = self::$messaging->topics();

        $request = self::$messaging->getClient()->getRequest();
        $response = self::$messaging->getClient()->getResponse();

        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('/topics', $request->getPath());
        $this->assertNotEmpty($request->getHeader('X-Auth-Token'));

        $this->assertEquals(true, is_array($topics));
        $this->assertEquals('SoftLayer_Messaging_Topic', get_class(array_shift($topics)));

        // It's fine if this is empty, but the document should always have
        // basic structure.
        $this->assertGreaterThanOrEqual(0, $response->getBody()->item_count);
        $this->assertCount($response->getBody()->item_count, $response->getBody()->items);
    }

    public function testCreateQueueAndHttpEndpointSubscriptions()
    {
        $topicName = self::topicName();
        $queueName = self::queueName();

        self::$messaging->topic($topicName)->create();

        sleep(WAIT);

        // Create an HTTP endpoint
        $http_endpoint = new SoftLayer_Messaging_Endpoint_Http();
        $http_endpoint->setMethod("POST");
        $http_endpoint->setUrl("http://www.example.com/");
        $http_endpoint->setParams(array('param1' => 'value1'));
        $http_endpoint->setHeaders(array('header1' => 'value1'));
        $http_endpoint->setBody("Example Body");

        self::$messaging->topic($topicName)->subscription()
            ->setEndpointType('http')
            ->setEndpoint($http_endpoint)
            ->create();

        $request = self::$messaging->getClient()->getRequest();
        $response = self::$messaging->getClient()->getResponse();

        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals("/topics/{$topicName}/subscriptions", $request->getPath());

        // First, we need a queue
        self::$messaging->queue($queueName)->create();

        sleep(WAIT);

        // Create a Queue endpoint
        $queue_endpoint = new SoftLayer_Messaging_Endpoint_Queue();
        $queue_endpoint->setQueueName($queueName);

        self::$messaging->topic($topicName)->subscription()
            ->setEndpointType('queue')
            ->setEndpoint($queue_endpoint)
            ->create();
    
        sleep(WAIT);

        $subscriptions = self::$messaging->topic($topicName)->subscriptions();

        $this->assertGreaterThanOrEqual(2, count($subscriptions));
    }
}
