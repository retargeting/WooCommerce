<?php

namespace RetargetingSDK;

use RetargetingSDK\Helpers\CustomersApiHelper;
use RetargetingSDK\Helpers\EncryptionHelper;

/**
 * Class CustomersFeed
 * @package RetargetingSDK
 */
class CustomersFeed extends AbstractRetargetingSDK
{
    /**
     * @var mixed
     */
    protected $token;

    /**
     * @var EncryptionHelper
     */
    protected $encryption;

    /**
     * @var array
     */
    protected $customers = [];

    /**
     * @var int
     */
    protected $currentPage = 1;

    /**
     * @var string
     */
    protected $lastPage = '';

    /**
     * @var string
     */
    protected $nextPage = '';

    /**
     * @var string
     */
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

        $this->encryption = new EncryptionHelper($token);
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
     * @param $customer
     * @param bool $encrypt
     */
    public function addCustomer($customer, $encrypt = true)
    {
        $this->customers[] = $encrypt ? $this->encryption->encrypt($customer) : $customer;
    }

    /**
     * @return array
     */
    public function getCustomers()
    {
        return $this->customers;
    }

    /**
     * @param array $customers
     */
    public function setCustomers($customers)
    {
        $this->customers = $customers;
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
     * @param bool $encoded
     * @return array|string
     */
    public function getData($encoded = true)
    {
        $data = [
            'data'          => $this->getCustomers(),
            "current_page"  => $this->getCurrentPage(),
            "last_page"     => $this->getLastPage(),
            "next_page"     => $this->getNextPage(),
            "prev_page"     => $this->getPrevPage()
        ];

        return $encoded ? $this->toJSON($data) : $data;
    }
}
