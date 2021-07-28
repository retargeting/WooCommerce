<?php

namespace RetargetingSDK\Javascript\Item;

/**
 * Class AbstractItem
 * @package RetargetingSDK\Javascript\Item
 */
abstract class AbstractItem
{
    /**
     * @var string
     */
    protected $params;

    /**
     * @var string
     */
    protected $method;

    /**
     * @return mixed
     */
    public function getParams()
    {
        return !empty($this->params) ? $this->params : null;
    }

    /**
     * @param mixed $params
     * @return $this
     */
    public function setParams($params)
    {
        $this->params = $params;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return !empty($this->method) ? $this->method : null;
    }

    /**
     * @param mixed $method
     * @return $this
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @param $jsonData
     * @return bool
     */
    public function isNotEmptyJSON($jsonData)
    {
        return (!empty($jsonData) && !in_array($jsonData, ['[]', '{}']));
    }
}