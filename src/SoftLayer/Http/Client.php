<?php
namespace SoftLayer\Http;

use SoftLayer\Http\Adapter\AdapterInterface;
use SoftLayer\Http\Middleware\MiddlewareInterface;

class Client
{
    /** @var string */
    protected $baseUrl = '';
    /** @var MiddlewareInterface[] */
    protected $middleware = array();
    /** @var array */
    protected $defaultHeaders = array();
    /** @var AdapterInterface */
    protected $adapter;
    /** @var Request */
    protected $request;
    /** @var Response */
    protected $response;

    /** @return Client */
    public static function getClient()
    {
        $client = new Client();
        $client->setAdapter(new Adapter\Curl());
        $client->addMiddleware(new Middleware\Core());
        $client->addMiddleware(new Middleware\Json());

        return $client;
    }

    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    public function get($path, $options = array())
    {
        $this->call('GET', $path, $options);

        return $this->getResponse();
    }

    public function put($path, $options = array())
    {
        $this->call('PUT', $path, $options);

        return $this->getResponse();
    }

    public function post($path, $options = array())
    {
        $this->call('POST', $path, $options);

        return $this->getResponse();
    }

    public function delete($path, $options = array())
    {
        $this->call('DELETE', $path, $options);

        return $this->getResponse();
    }

    public function setAdapter(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /** @return AdapterInterface */
    public function getAdapter()
    {
        return $this->adapter;
    }

    public function addMiddleware($middleware)
    {
        $this->middleware[] = $middleware;
    }

    public function setDefaultHeader($header, $value)
    {
        $this->defaultHeaders[$header] = $value;
    }

    /** @return Request */
    public function getRequest()
    {
        if (! $this->request) {
            $this->request = new Request();
        }

        return $this->request;
    }

    /** @return Response */
    public function getResponse()
    {
        if (! $this->response) {
            $this->response = new Response();
        }

        return $this->response;
    }

    protected function call($method, $path, $options = array())
    {
        $defaults = array(
            'headers' => array(),
            'params' => array(),
            'body' => '',
        );

        $options = array_merge($defaults, $options);

        // Localize our request and response for the call,
        // which also allows us to pass by reference for our
        // middleware.
        $request = $this->getRequest();
        $response = $this->getResponse();

        $request->setBaseUrl($this->baseUrl);
        $request->setMethod($method);
        $request->setPath($path);
        $request->setParams($options['params']);
        $request->setHeaders($options['headers']);
        $request->setBody($options['body']);

        foreach ($this->defaultHeaders as $header => $value) {
            $request->setHeader($header, $value);
        }

        foreach ($this->middleware as $middleware) {
            $middleware->filterRequest($request);
        }

        $this->adapter->call($request, $response);

        foreach (array_reverse($this->middleware) as $middleware) {
            $middleware->filterResponse($response);
        }

        $this->setRequest($request);
        $this->setResponse($response);
    }

    /** @param Request $request */
    protected function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /** @param Response $response */
    protected function setResponse(Response $response)
    {
        $this->response = $response;
    }
}
