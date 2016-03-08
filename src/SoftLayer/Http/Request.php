<?php
namespace SoftLayer\Http;

class Request extends Base
{
    private $params = array();

    public function setParams($params)
    {
        $this->params = $params;
    }

    public function getParams()
    {
        return $this->params;
    }
}
