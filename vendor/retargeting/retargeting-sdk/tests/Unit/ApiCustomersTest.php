<?php
/**
 * Created by PhpStorm.
 * User: bratucornel
 * Date: 2019-02-22
 * Time: 10:25
 */

namespace RetargetingSDK;

use PHPUnit\Framework\TestCase;
use RetargetingSDK\Api\Customers;
use RetargetingSDK\Helpers\DecryptionHelper;
use RetargetingSDK\Helpers\EncryptionHelper;

/**
 * Class ApiCustomersTest
 * @package Tests\Unit
 * @property Customers customersInstance
 */
class ApiCustomersTest extends TestCase
{
    const TOKEN = "df2ce5cba06265db9bffeb6caf8d9fcf46a5a1712f774bca67535a82bdcf1955";

    protected $customer = [
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'john.doe@example.com',
        'phone' => '0770000000',
        'status' => true
    ];

    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        $this->customersInstance = new Customers(self::TOKEN);

        $this->customersInstance->setToken(self::TOKEN);
        $this->customersInstance->setData($this->customer);
        $this->customersInstance->setCurrentPage(2);
        $this->customersInstance->setLastPage(120);
        $this->customersInstance->setPrevPage('https://www.example.com/api/retargetingtracker?page=1');
        $this->customersInstance->setNextPage('https://www.example.com/api/retargetingtracker?page=3');
    }

    /**
     * Test if customer has data
     */
    public function testIfCustomersHasData()
    {
        $this->assertNotEmpty($this->customersInstance->getData());
    }

    /**
     * Test if token is not null
     */
    public function testIfCustomerHasToken()
    {
        $this->assertNotNull($this->customersInstance->getToken());
    }

    /**
     * Test if page related data is not empty
     */
    public function testIfCustomerHasPageData()
    {
        $this->assertNotNull($this->customersInstance->getCurrentPage());
        $this->assertNotNull($this->customersInstance->getLastPage());
        $this->assertNotNull($this->customersInstance->getNextPage());
        $this->assertNotNull($this->customersInstance->getPrevPage());
    }

    /**
     * Test if token is type of hashed
     * @throws Exceptions\DecryptException
     * @throws Exceptions\RTGException
     */
    public function testIfCustomerDataIsHashed()
    {
        $encryption = new EncryptionHelper(self::TOKEN);

        $data = $encryption->encrypt(json_encode($this->customer, JSON_PRETTY_PRINT));

        $this->customersInstance->setData($data);

        $decryption = new DecryptionHelper(self::TOKEN);

        $decryptedData = $decryption->decrypt($this->customersInstance->getData());

        $this->assertEquals(json_decode($decryptedData, JSON_PRETTY_PRINT), $this->customer);
    }

    /**
     * Test if customer prepare api information has proper format
     * @throws \Exception
     */
    public function testIfCustomerPrepareApiInfoHasProperFormat()
    {
        $this->assertEquals($this->customersInstance->prepareCustomersApiInfo(), json_encode([
            'data' => $this->customer,
            'current_page' => 2,
            'last_page' => 120,
            'next_page' => 'https://www.example.com/api/retargetingtracker?page=3',
            'prev_page' => 'https://www.example.com/api/retargetingtracker?page=1'
        ], JSON_PRETTY_PRINT));
    }
}
