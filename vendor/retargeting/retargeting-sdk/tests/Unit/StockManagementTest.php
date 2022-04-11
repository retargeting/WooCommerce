<?php
/**
 * Created by PhpStorm.
 * User: andreicotaga
 * Date: 2019-03-28
 * Time: 10:38
 */

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use RetargetingSDK\Api\StockManagement;

/**
 * Class StockManagementTest
 * @package Tests\Unit
 * @property StockManagement stock
 */
class StockManagementTest extends TestCase
{
    protected $stockSample = [
        'productId' => 'HAF220',
        'name' => 'Samsung Galaxy Tab 10.0',
        'price' => '990.90',
        'promo' => '870.20',
        'image' => 'https://www.example.com/catalog/image/samsung-galaxy-tab.png',
        'url'   => 'https://www.example.com/tablets/samsung-galaxy-tab',
        'stock' => true
    ];

    public function setUp(): void
    {
        $this->stock = new StockManagement();

        $this->stock->setProductId('HAF220');
        $this->stock->setName('Samsung Galaxy Tab 10.0');
        $this->stock->setPrice(990.90);
        $this->stock->setPromo(870.20);
        $this->stock->setImage('https://www.example.com/catalog/image/samsung-galaxy-tab.png');
        $this->stock->setUrl('https://www.example.com/tablets/samsung-galaxy-tab');
        $this->stock->setStock(true);
    }

    /**
     * Test if stock management has product Id
     */
    public function testIfStockManagementHasProductId()
    {
        $this->assertNotNull($this->stock->getProductId());
    }

    /**
     * Test if stock management has name
     */
    public function testIfStockManagementHasName()
    {
        $this->assertNotNull($this->stock->getName());
    }

    /**
     * Test if stock management has price
     */
    public function testIfStockManagementHasPrice()
    {
        $this->assertNotNull($this->stock->getPrice());
    }

    /**
     * Test if stock management has promo
     */
    public function testIfStockManagementHasPromo()
    {
        $this->assertNotNull($this->stock->getPromo());
    }

    /**
     * Test if stock management has image
     */
    public function testIfStockManagementHasImage()
    {
        $this->assertNotNull($this->stock->getImage());
    }

    /**
     * Test if stock management has url
     */
    public function testIfStockManagementHasUrl()
    {
        $this->assertNotNull($this->stock->getUrl());
    }

    /**
     * Test if stock management is bool
     */
    public function testIfStockManagementIsBool()
    {
        $this->assertIsBool($this->stock->isStock());
    }

    /**
     * Test if stock management prepare info has proper format
     */
    public function testIfStockManagementPrepareStockInfoHasProperFormat()
    {
        $this->assertEquals($this->stock->prepareStockInfo(), $this->stockSample);
    }
}