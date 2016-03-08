<?php
require_once 'bootstrap.php';

class AuthTest extends BaseTest
{
    public function testAuthentication()
    {
        $request = self::$messaging->getClient()->getRequest();
        $response = self::$messaging->getClient()->getResponse();

        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals(QUEUE_USERNAME, $request->getHeader('X-Auth-User'));
        $this->assertEquals(QUEUE_API_KEY, $request->getHeader('X-Auth-Key'));

        $this->assertObjectHasAttribute('token', $response->getBody());
        $this->assertObjectHasAttribute('status', $response->getBody());
    }
}
