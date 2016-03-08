<?php
require_once 'bootstrap.php';

class PingTest extends BaseTest
{
    public function testPing()
    {
        self::$messaging->ping();

        $request = self::$messaging->getClient()->getRequest();
        $response = self::$messaging->getClient()->getResponse();

        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('/ping', $request->getPath());
        $this->assertEquals('OK', $response->getBody());
    }
}
