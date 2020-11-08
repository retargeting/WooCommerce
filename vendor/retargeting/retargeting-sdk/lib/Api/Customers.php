<?php
/**
 * Created by PhpStorm.
 * User: bratucornel
 * Date: 2019-03-13
 * Time: 12:24
 */

namespace RetargetingSDK\Api;

use RetargetingSDK\AbstractRetargetingSDK;
use RetargetingSDK\Helpers\CustomersApiHelper;

/**
 * Class Customers
 * @package Retargeting\Api
 */
class Customers extends AbstractRetargetingSDK
{
    protected $token;
    protected $data = [];
    protected $currentPage = 1;
    protected $lastPage = '';
    protected $nextPage = '';
    protected $prevPage = '';

    /**
     * Customers constructor.
     * @param $token
     * @throws \Exception
     */
    public function __construct($token)
    {
        $token = CustomersApiHelper::getToken($token);

        $this->token = $token;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * @param mixed $currentPage
     */
    public function setCurrentPage($currentPage)
    {
        $this->currentPage = $currentPage;
    }

    /**
     * @return mixed
     */
    public function getLastPage()
    {
        return $this->lastPage;
    }

    /**
     * @param mixed $lastPage
     */
    public function setLastPage($lastPage)
    {
        $this->lastPage = $lastPage;
    }

    /**
     * @return mixed
     */
    public function getNextPage()
    {
        return $this->nextPage;
    }

    /**
     * @param mixed $nextPage
     */
    public function setNextPage($nextPage)
    {
        $this->nextPage = $nextPage;
    }

    /**
     * @return mixed
     */
    public function getPrevPage()
    {
        return $this->prevPage;
    }

    /**
     * @param mixed $prevPage
     */
    public function setPrevPage($prevPage)
    {
        $this->prevPage = $prevPage;
    }

    /**
     * Prepare customers api information
     * @throws \Exception
     */
    public function prepareCustomersApiInfo()
    {
        return $this->toJSON([
            'data'          => $this->getData(),
            "current_page"  => $this->getCurrentPage(),
            "last_page"     => $this->getLastPage(),
            "next_page"     => $this->getNextPage(),
            "prev_page"     => $this->getPrevPage()
        ]);
    }
}
