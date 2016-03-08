<?php
namespace SoftLayer\Http;

class Response extends Base
{
    private $status;

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }
}
