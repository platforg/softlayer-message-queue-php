<?php
namespace SoftLayer\Http\Adapter;

use SoftLayer\Http;

interface AdapterInterface
{
    public function call(Http\Request &$request, Http\Response &$response);
}
