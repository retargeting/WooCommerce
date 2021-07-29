<?php

namespace RetargetingSDK;

/**
 * Class AbstractCredentials
 */
abstract class AbstractCredentials
{
    /**
     * @var string
     */
    private $trackingApiKey;

    /**
     * @var string
     */
    private $restApiKey;

    /**
     * @return string
     */
    public function getTrackingApiKey()
    {
        return $this->trackingApiKey;
    }

    /**
     * @param string $trackingApiKey
     * @return $this
     */
    public function setTrackingApiKey($trackingApiKey)
    {
        $this->trackingApiKey = $trackingApiKey;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasTrackingApiKey()
    {
        return !empty($this->trackingApiKey);
    }

    /**
     * @return string
     */
    public function getRestApiKey()
    {
        return $this->restApiKey;
    }

    /**
     * @param string $restApiKey
     * @return $this
     */
    public function setRestApiKey($restApiKey)
    {
        $this->restApiKey = $restApiKey;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasRestApiKey()
    {
        return !empty($this->restApiKey);
    }
}