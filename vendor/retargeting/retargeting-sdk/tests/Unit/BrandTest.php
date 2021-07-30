<?php
/**
 * Created by PhpStorm.
 * User: andreicotaga
 * Date: 2019-03-15
 * Time: 17:40
 */

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use RetargetingSDK\Brand;

/**
 * Class BrandTest
 * @package Tests\Unit
 * @property Brand brand
 */
class BrandTest extends TestCase
{
    public function setUp(): void
    {
        $this->brand = new Brand();
    }

    /**
     * Check if brand has id
     */
    public function testIfBrandHasId()
    {
        $this->brand->setId(33);
        $this->assertNotNull($this->brand->getId());
    }

    /**
     * Check if brand has name
     */
    public function testIfBrandHasName()
    {
        $this->brand->setName('Nike');
        $this->assertNotNull($this->brand->getName());
    }

    /**
     * Check if brand prepare information is json
     */
    public function testIfBrandPrepareInformationIsJson()
    {
        $this->brand->setId(23);
        $this->brand->setName('Apple');

        $this->assertJson($this->brand->prepareBrandInformation());
    }

    /**
     * Check if brand has proper json format
     */
     public function testIfBrandHasProperFormat()
     {
         $this->brand->setId(9000);
         $this->brand->setName('Adidas');

         $this->assertEquals($this->brand->prepareBrandInformation(), json_encode(['id' => 9000, 'name' => 'Adidas'], JSON_PRETTY_PRINT));
     }
}
