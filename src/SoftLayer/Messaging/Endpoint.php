<?php
namespace SoftLayer\Messaging;

class Endpoint
{
    public static function endpointByType($type)
    {
        switch (strtolower($type)) {
            case 'http':
                return new Endpoint\Http();
                break;
            case 'queue':
                return new Endpoint\Queue();
                break;
        }

        throw new \Exception(sprintf('"%s" is not a valid endpoint type!'));
    }
}
