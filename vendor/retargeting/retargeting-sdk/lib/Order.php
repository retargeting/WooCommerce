<?php
/**
 * Created by PhpStorm.
 * User: bratucornel
 * Date: 2019-02-19
 * Time: 08:03
 */

namespace RetargetingSDK;

use RetargetingSDK\Helpers\EmailHelper;

class Order extends AbstractRetargetingSDK
{
    protected $orderNo;
    protected $lastName = '';
    protected $firstName = '';
    protected $email = '';
    protected $phone = 0;
    protected $state = '';
    protected $city = '';
    protected $address = '';
    protected $birthday = '';
    protected $discount = '';
    protected $discountCode = '0';
    protected $shipping = '';
    protected $rebates = 0;
    protected $fees = 0;
    protected $total = 0;
    protected $products = [];

    /**
     * @return mixed
     */
    public function getOrderNo()
    {
        return $this->orderNo;
    }

    /**
     * @param mixed $orderNo
     */
    public function setOrderNo($orderNo)
    {
        $orderNo = $this->formatIntFloatString($orderNo);

        $this->orderNo = $orderNo;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName)
    {
        $lastName = $this->getProperFormattedString($lastName);

        $this->lastName = $lastName;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName)
    {
        $firstName = $this->getProperFormattedString($firstName);

        $this->firstName = $firstName;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $email = EmailHelper::sanitize($email, 'email');

        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $phone = $this->getProperFormattedString($phone);

        $this->phone = $phone;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param mixed $state
     */
    public function setState($state)
    {
        $state = $this->getProperFormattedString($state);

        $this->state = $state;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city)
    {
        $city = $this->getProperFormattedString($city);

        $this->city = $city;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $address = $this->getProperFormattedString($address);

        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @param $birthday
     * @throws \Exception
     */
    public function setBirthday($birthday)
    {
        $this->birthday = EmailHelper::validateBirthday($birthday);
    }

    /**
     * @return mixed
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @param mixed $discount
     */
    public function setDiscount($discount)
    {
        $discount = $this->getProperFormattedString($discount);

        $this->discount = $discount;
    }

    /**
     * @return mixed
     */
    public function getDiscountCode()
    {
        return $this->discountCode;
    }

    /**
     * @param mixed $discountCode
     */
    public function setDiscountCode($discountCode)
    {
        $discountCode = $this->getProperFormattedString($discountCode);

        $this->discountCode = $discountCode;
    }

    /**
     * @return mixed
     */
    public function getShipping()
    {
        return $this->shipping;
    }

    /**
     * @param mixed $shipping
     */
    public function setShipping($shipping)
    {
        $shipping = $this->getProperFormattedString($shipping);

        $this->shipping = $shipping;
    }

    /**
     * @return int
     */
    public function getRebates()
    {
        return $this->rebates;
    }

    /**
     * @param $rebates
     */
    public function setRebates($rebates)
    {
        $this->rebates = $rebates;
    }

    /**
     * @return int
     */
    public function getFees()
    {
        return $this->fees;
    }

    /**
     * @param $fees
     */
    public function setFees($fees)
    {
        $this->fees = $fees;
    }

    /**
     * @return mixed
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param mixed $total
     */
    public function setTotal($total)
    {
        $this->formatIntFloatString($total);

        $this->total = $total;
    }

    /**
     * @param $id
     * @param $qnt
     * @param $price
     * @param string $variationCode
     */
    public function setProduct($id, $qnt, $price, $variationCode = '')
    {
        $this->products[] = [
            'id'             => $id,
            'quantity'       => $qnt,
            'price'          => $this->formatIntFloatString($price),
            'variation_code' => !empty($variationCode) ? $variationCode : ''
        ];
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
     * @param bool $encoded
     * @return array|string
     */
    public function getData($encoded = true)
    {
        $order = [
            'order_no'      => $this->getOrderNo(),
            'lastname'      => $this->getLastName(),
            'firstname'     => $this->getFirstName(),
            'email'         => $this->getEmail(),
            'phone'         => $this->getPhone(),
            'state'         => $this->getState(),
            'city'          => $this->getCity(),
            'address'       => $this->getAddress(),
            'birthday'      => $this->getBirthday(),
            'discount'      => $this->getDiscount(),
            'discount_code' => $this->getDiscountCode(),
            'shipping'      => $this->getShipping(),
            'rebates'       => $this->getRebates(),
            'fees'          => $this->getFees(),
            'total'         => $this->getTotal()
        ];

        return $encoded ? $this->toJSON($order) : $order;
    }

    /**
     * @param bool $encoded
     * @return array|string
     */
    public function getProductsData($encoded = true)
    {
        return $encoded ? $this->toJSON($this->getProducts()) : $this->getProducts();
    }
}