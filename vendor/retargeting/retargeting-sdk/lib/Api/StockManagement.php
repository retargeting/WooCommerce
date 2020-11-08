<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 3/24/2019
 * Time: 7:39 PM
 */

namespace RetargetingSDK\Api;

use RetargetingSDK\AbstractRetargetingSDK;
use RetargetingSDK\Exceptions\RTGException;

class StockManagement extends AbstractRetargetingSDK
{
    protected $productId;
    protected $name;
    protected $price;
    protected $promo;
    protected $image;
    protected $url;
    protected $stock = false;

    /**
     * @return mixed
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @param mixed $productId
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return mixed
     */
    public function getPromo()
    {
        return $this->promo;
    }

    /**
     * @param mixed $promo
     */
    public function setPromo($promo)
    {
        $this->promo = $promo;
    }

    /**
     * @return bool
     */
    public function isStock()
    {
        return $this->stock;
    }

    /**
     * @param bool $stock
     */
    public function setStock($stock)
    {
        $this->stock = $stock;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Prepare stock information
     * @return array
     */
    public function prepareStockInfo()
    {
        return [
            'productId' => $this->getProductId(),
            'name'      => $this->getName(),
            'price'     => $this->getPrice(),
            'promo'     => $this->getPromo(),
            'image'     => $this->getImage(),
            'url'       => $this->getUrl(),
            'stock'     => $this->isStock(),
        ];
    }

    /**
     * Update product stock
     * @param $api
     * @param $product
     * @throws RTGException
     */
    public function updateStock($api, $product)
    {
        try {
            $rtgClient = new Client($api);
            $rtgClient->setResponseFormat("json");
            $rtgClient->setDecoding(false);
            $rtgClient->products->update($product);
        } catch(RTGException $exception) {
            throw new RTGException($exception->getMessage());
        }
    }
}