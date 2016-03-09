<?php
namespace SoftLayer\Messaging;

class Subscription extends Entity
{
    protected static $emit = array('id', 'endpoint_type', 'endpoint');

    protected $id;
    protected $endpointType = '';
    protected $endpoint = null;

    public function getId()
    {
        return $this->id;
    }

    public function setEndpointType($endpointType)
    {
        $this->endpointType = $endpointType;

        return $this;
    }

    public function getEndpointType()
    {
        return $this->endpointType;
    }

    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    public function getEndpoint()
    {
        return $this->endpoint;
    }

    public function create()
    {
        $this->getClient()->post(
            sprintf('/topics/%s/subscriptions', $this->getParent()->getName()),
            array('body' => $this->serialize())
        );

        return $this;
    }

    public function delete($id = null)
    {
        $this->getClient()->delete(
            sprintf(
                '/%ss/%s/subscriptions/%s',
                $this->getParent()->getShortType(),
                $this->getParent()->getName(),
                $id ? $id : $this->getId()
            )
        );

        return $this;
    }
}
