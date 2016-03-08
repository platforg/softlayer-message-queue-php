<?php
namespace SoftLayer\Http\Middleware;

use SoftLayer\Http;

interface MiddlewareInterface
{
    public function filterRequest(Http\Request &$request);
    public function filterResponse(Http\Response &$response);
}