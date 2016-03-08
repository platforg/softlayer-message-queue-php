<?php
require_once 'bootstrap.php';

class MessageTest extends BaseTest
{
    public function testCreateMessage()
    {
        $queueName = self::queueName();

        self::$messaging->queue($queueName)->create();
        self::$messaging->queue($queueName)->message()->setBody('Example body')->create();
        
        $request = self::$messaging->getClient()->getRequest();
        $response = self::$messaging->getClient()->getResponse();

        $this->assertEquals("POST", $request->getMethod());
        $this->assertEquals("/queues/{$queueName}/messages", $request->getPath());

        $this->assertEquals(201, $response->getStatus());
    }

    public function testPopMessages()
    {
        $queueName = self::queueName();

        self::$messaging->queue($queueName)->create();
        self::$messaging->queue($queueName)->message()->setBody('Example 1')->create();
        self::$messaging->queue($queueName)->message()->setBody('Example 2')->create();
        self::$messaging->queue($queueName)->message()->setBody('Example 3')->create();

        sleep(WAIT);

        $this->assertEquals(3, self::$messaging->queue($queueName)->getMessageCount(true));
        $this->assertEquals(3, self::$messaging->queue($queueName)->getVisibleMessageCount(true));

        $messages = self::$messaging->queue($queueName)->messages(3);

        $this->assertEquals(3, count($messages));

        $request = self::$messaging->getClient()->getRequest();
        $response = self::$messaging->getClient()->getResponse();

        $this->assertEquals("GET", $request->getMethod());
        $this->assertEquals("/queues/{$queueName}/messages", $request->getPath());

        $this->assertEquals(200, $response->getStatus());
        $this->assertEquals(3, $response->getBody()->item_count);
        $this->assertEquals(3, count($response->getBody()->items));
    }

    public function testPushALotOfMessages()
    {
        $queueName = self::queueName();

        self::$messaging->queue($queueName)->create();

        $failed = 0;

        for($i = 0; $i < 100; $i++) {
            self::$messaging->queue($queueName)->message()->setBody("Example {$i}")->create();

            if(self::$messaging->getClient()->getResponse()->getStatus() >= 400) {
                $failed += 1;
            }
        }

        $this->assertEquals(0, $failed);
    }
}

