<?php
/**
 * Created by PhpStorm.
 * User: andreicotaga
 * Date: 2019-03-15
 * Time: 17:27
 */

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use RetargetingSDK\Email;

/**
 * @property Email email
 */
class EmailTest extends TestCase
{
    public function setUp(): void
    {
        $this->email = new Email();

        $this->email->setEmail('john.doe@mail.com');
        $this->email->setName('John Doe');
        $this->email->setPhone('(298) 407-4029');
        $this->email->setCity('Berlin');
        $this->email->setSex(0);
        $this->email->setBirthday('20-02-1960');
    }

    /**
     * Test if email is not empty
     */
    public function test_if_email_is_not_empty()
    {
        $this->assertNotNull($this->email->getEmail());
    }

    /**
     * Test if email has proper format
     */
    public function test_if_email_has_proper_format()
    {
        $this->assertRegExp('/^.+\@\S+\.\S+$/', $this->email->getEmail());
    }

    /**
     * Test if name is not empty
     */
    public function test_if_name_is_not_empty()
    {
        $this->assertNotNull($this->email->getName());
    }

    /**
     * Test if name is string
     */
    public function test_if_name_is_string()
    {
        $this->assertIsString($this->email->getName());
    }

    /**
     * Test if phone number is not empty
     */
    public function test_if_phone_is_not_empty()
    {
        $this->assertNotNull($this->email->getPhone());
    }

    /**
     * Test if phone number is not empty
     */
    public function test_if_phone_is_string()
    {
        $this->assertIsString($this->email->getPhone());
    }

    /**
     * Test if city number is not empty
     */
    public function test_if_city_is_not_empty()
    {
        $this->assertNotNull($this->email->getCity());
    }

    /**
     * Test if city number is not empty
     */
    public function test_if_city_is_string()
    {
        $this->assertIsString($this->email->getCity());
    }

    /**
     * Test if city has proper format
     */
    public function test_if_city_has_proper_format()
    {
        $this->assertEquals($this->email->getCity(), 'Berlin');
    }

    /**
     * Test if sex is not empty
     */
    public function test_if_sex_is_not_empty()
    {
        $this->assertNotNull($this->email->getSex());
    }

    /**
     * Test if sex is of type boolean
     */
    public function test_if_sex_is_boolean()
    {
        $this->assertIsNumeric($this->email->getSex());
    }

    /**
     * Test if birthday is not empty
     * @throws \Exception
     */
    public function test_if_birthday_is_not_empty()
    {
        $this->assertNotNull($this->email->getBirthday());
    }

    /**
     * Test if birthday has correct format
     */
    public function test_if_birthday_has_proper_format()
    {
        $this->assertEquals($this->email->getBirthday(), '20-02-1960');
    }

    /**
     * Test if prepare email data return proper formatted json
     * @throws \Exception
     */
    public function test_if_prepare_email_data_has_proper_format()
    {
        $this->assertEquals($this->email->prepareEmailData(),
            json_encode([
                'email' => 'john.doe@mail.com',
                'name'  => 'John Doe',
                'phone' => '(298) 407-4029',
                'city'  => 'Berlin',
                'sex'   => 0,
                'birthday'   => '20-02-1960'
            ], JSON_PRETTY_PRINT)
        );
    }
}