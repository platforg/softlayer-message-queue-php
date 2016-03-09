<?php
namespace SoftLayer\Http\Middleware;

use SoftLayer\Http;

class Core implements MiddlewareInterface
{
    public function filterRequest(Http\Request &$request)
    {
    }

    public function filterResponse(Http\Response &$response)
    {
        $status = $response->getStatus();

        if ($status >= 400) {
            $body = $response->getBody();
            $errors = '';
            $exception = "[{$status}]";

            if (property_exists($body, 'message')) {
                $exception .= " - {$body->message}";
            }

            if (property_exists($body, 'errors')) {
                foreach ($body->errors as $category => $collection) {
                    $errors .= "{$category}: " . implode(', ', $collection);
                }
            }

            if ($errors) {
                $exception .= " - {$errors}";
            }

            throw new \Exception($exception);
        }
    }
}
