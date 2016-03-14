<?php
require_once 'bootstrap.php';

class QueueTest extends BaseTest
{
    public function testQueuesList()
    {
        $queueName = self::queueName();

        self::$messaging->queue($queueName)->create();

        $queues = self::$messaging->queues();

        $request = self::$messaging->getClient()->getRequest();
        $response = self::$messaging->getClient()->getResponse();

        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('/queues', $request->getPath());
        $this->assertNotEmpty($request->getHeader('X-Auth-Token'));

        $this->assertEquals(true, is_array($queues));
        $this->assertEquals('SoftLayer\Messaging\Queue', get_class(array_shift($queues)));

        // It's fine if this is empty, but the document should always have
        // basic structure.
        $this->assertGreaterThanOrEqual(0, $response->getBody()->item_count);
        $this->assertCount($response->getBody()->item_count, $response->getBody()->items);
    }

    public function testQueueCreationAndFetching()
    {
        $queueName = self::queueName();

        self::$messaging->queue($queueName)
            ->setVisibilityInterval(30)
            ->setExpiration(604800)
            ->addTag('tag1')
            ->addTag('tag2')
            ->create();

        $this->assertEquals(201, self::$messaging->getClient()->getResponse()->getStatus());
        $this->assertEquals($queueName, self::$messaging->queue($queueName)->fetch()->getName());

        $request = self::$messaging->getClient()->getRequest();
        $response = self::$messaging->getClient()->getResponse();

        $body = $response->getBody();

        $this->assertEquals($queueName, $body->name);
        $this->assertEquals(30, $body->visibility_interval);
        $this->assertEquals(604800, $body->expiration);
        $this->assertEquals(array('tag1', 'tag2'), $body->tags);
    }

    public function testQueueCreationAndDeletion()
    {
        $queueName = self::queueName();

        // Test creation
        self::$messaging->queue()
            ->setName($queueName)
            ->create();

        $request = self::$messaging->getClient()->getRequest();
        $response = self::$messaging->getClient()->getResponse();

        $this->assertEquals('PUT', $request->getMethod());
        $this->assertEquals('Object created', $response->getBody()->message);

        // ...and deletion. This will cause us not to be able to clean it up
        // automatically.
        self::$messaging->queue($queueName)->delete();

        $request = self::$messaging->getClient()->getRequest();
        $response = self::$messaging->getClient()->getResponse();

        $this->assertEquals('DELETE', $request->getMethod());
        $this->assertEquals('Object queued for deletion', $response->getBody()->message);
    }

    public function testQueueUpdate()
    {
        $queueName = self::queueName();

        self::$messaging->queue()
            ->setName($queueName)
            ->setVisibilityInterval(100)
            ->create();

        // May be just updating it if the queue already exists.
        $this->assertContains(self::$messaging->getClient()->getResponse()->getStatus(), array(200, 201));

        $queue = self::$messaging->queue($queueName)->fetch();

        // Are we getting back what we gave it?
        $this->assertEquals($queueName, $queue->getName());
        $this->assertEquals(100, $queue->getVisibilityInterval());
    }
}
