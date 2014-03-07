<?php

set_include_path(get_include_path().PATH_SEPARATOR.dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'src');

require_once 'SoftLayer/Messaging.php';

define('QUEUE_ACCOUNT', getenv('QUEUE_ACCOUNT'));
define('QUEUE_USERNAME', getenv('QUEUE_USERNAME'));
define('QUEUE_API_KEY', getenv('QUEUE_API_KEY'));
define('WAIT', 5);

abstract class BaseTest extends PHPUnit_Framework_TestCase
{
    public static $messaging;
    public static $queues = array();
    public static $topics = array();

    public static function setUpBeforeClass()
    {
        self::$messaging = new SoftLayer_Messaging();
        self::$messaging->authenticate(QUEUE_ACCOUNT, QUEUE_USERNAME, QUEUE_API_KEY);
        self::$queues = array();
        self::$topics = array();
    }

    public static function queueName()
    {
        array_unshift(self::$queues, "testQueue_".rand(0, 99999));
        return self::$queues[0];
    }

    public static function topicName()
    {
        array_unshift(self::$topics, "testTopic_".rand(0, 99999));
        return self::$topics[0];
    }    

    public static function tearDownAfterClass()
    {
        foreach(self::$queues as $queue) {
            try {
                self::$messaging->queue($queue)->delete(true);
            } catch(Exception $e) {
                echo "Could not clean up: $queue (Perhaps a test deleted it?)";
            }
        }

        foreach(self::$topics as $topic) {
            try {
                self::$messaging->topic($topic)->delete(true);
            } catch(Exception $e) {
                echo "Could not clean up $topic (Perhaps a test deleted it?)";
            }
        }
    }
}
