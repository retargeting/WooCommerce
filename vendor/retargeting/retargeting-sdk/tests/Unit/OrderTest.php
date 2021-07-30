<?php
/**
 * Created by PhpStorm.
 * User: andreicotaga
 * Date: 2019-03-15
 * Time: 17:23
 */

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use RetargetingSDK\Order;

/**
 * @property Order order
 */
class OrderTest extends TestCase
{
    public function setUp(): void
    {
        $this->order = new Order();
        $this->order->setOrderNo(28);
        $this->order->setLastName('Doe');
        $this->order->setFirstName('John');
        $this->order->setEmail('john.doe@mail.com');
        $this->order->setPhone('40771445255');
        $this->order->setState('Germany');
        $this->order->setCity('Berlin');
        $this->order->setAddress('Sample address');
        $this->order->setBirthday('01-01-1990');
        $this->order->setDiscount(20);
        $this->order->setDiscountCode('RAX204');
        $this->order->setShipping('Sample shipping street');
        $this->order->setTotal(396);
    }

    /**
     * Test if order has order number
     */
    public function test_if_order_has_no()
    {
        $this->assertNotNull($this->order->getOrderNo());
    }

    /**
     * Test if order has last name
     */
    public function test_if_order_has_last_name()
    {
        $this->assertNotNull($this->order->getLastName());
    }

    /**
     * Test if order has first name
     */
    public function test_if_order_has_first_name()
    {
        $this->assertNotNull($this->order->getFirstName());
    }

    /**
     * Test if order has email
     */
    public function test_if_order_has_email()
    {
        $this->assertNotNull($this->order->getEmail());
    }

    /**
     * Test if order email is valid
     */
    public function test_if_order_has_valid_email()
    {
        $this->assertRegExp('/^.+\@\S+\.\S+$/', $this->order->getEmail());
    }

    /**
     * Test if order has phone
     */
    public function test_if_order_has_phone()
    {
        $this->assertNotNull($this->order->getPhone());
    }

    /**
     * Test if order has state
     */
    public function test_if_order_has_state()
    {
        $this->assertNotNull($this->order->getState());
    }

    /**
     * Test if order has city
     */
    public function test_if_order_has_city()
    {
        $this->assertNotNull($this->order->getCity());
    }

    /**
     * Test if order has address
     */
    public function test_if_order_has_address()
    {
        $this->assertNotNull($this->order->getAddress());
    }

    /**
     * Test if birthday is not empty
     * @throws \Exception
     */
    public function test_if_birthday_is_not_empty()
    {
        $this->assertNotNull($this->order->getBirthday());
    }

    /**
     * Test if birthday has correct format
     */
    public function test_if_birthday_has_proper_format()
    {
        $this->assertEquals($this->order->getBirthday(), '01-01-1990');
    }


    /**
     * Test if order has discount
     */
    public function test_if_order_has_discount()
    {
        $this->assertNotNull($this->order->getDiscount());
    }

    /**
     * Test if order has discount code
     */
    public function test_if_order_has_discount_code()
    {
        $this->assertNotNull($this->order->getDiscountCode());
    }

    /**
     * Test if order has shipping
     */
    public function test_if_order_has_shipping()
    {
        $this->assertNotNull($this->order->getShipping());
    }

    /**
     * Test if order has total
     */
    public function test_if_order_has_total()
    {
        $this->assertNotNull($this->order->getTotal());
    }

    /**
     * Test if order prepare information has correct json format
     */
    public function test_if_order_prepare_information_has_correct_json_format()
    {
        $order = [
            'order_no'  => 28,
            'lastname'  => 'Doe',
            'firstname' => 'John',
            'email'     => 'john.doe@mail.com',
            'phone'     => '40771445255',
            'state'     => 'Germany',
            'city'      => 'Berlin',
            'address'   => 'Sample address',
            'birthday'   => '01-01-1990',
            'discount'  => "20",
            'discount_code' => 'RAX204',
            'shipping'  => 'Sample shipping street',
            'total'     => 396
        ];

        $this->assertEquals($this->order->prepareOrderInformation(), json_encode($order, JSON_PRETTY_PRINT));
    }
}