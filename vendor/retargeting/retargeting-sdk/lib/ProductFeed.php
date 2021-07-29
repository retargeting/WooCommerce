<?php

namespace RetargetingSDK;

/**
 * Class ProductFeed
 * @package RetargetingSDK\Api
 */
class ProductFeed extends AbstractRetargetingSDK
{
    /**
     * @var array
     */
    protected $products = [];

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
     * @param $product
     */
    public function addProduct($product)
    {
        $this->products[] = $product;
    }

    /**
     * @return array
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param array $products
     */
    public function setProducts($products)
    {
        $this->products = $products;
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
            'data'          => $this->getProducts(),
            "current_page"  => $this->getCurrentPage(),
            "last_page"     => $this->getLastPage(),
            "next_page"     => $this->getNextPage(),
            "prev_page"     => $this->getPrevPage()
        ];

        return $encoded ? $this->toJSON($data) : $data;
    }
}
