<?php
/**
 * Created by PhpStorm.
 * User: andreicotaga
 * Date: 2019-03-15
 * Time: 17:36
 */

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use RetargetingSDK\Category;

/**
 * @property Category category
 */
class CategoryTest extends TestCase
{
    public function setUp(): void
    {
        $this->category = new Category();

        $this->category->setId(89);
        $this->category->setName('Shoes');
        $this->category->setParent('');
        $this->category->setUrl('https://www.example.com/desktops/monitors');
        $this->category->setBreadcrumb([
            ["id" => 21, "name" => "Sneakers", "parent" => 20],
            ["id" => 20, "name" => "Shoes", "parent" => false]
        ]);
    }

    /**
     * Check if category has identifier
     */
    public function testIfCategoryHasId()
    {
        $this->assertNotNull($this->category->getId());
    }

    /**
     * Check if category has name
     */
    public function testIfCategoryHasName()
    {
        $this->assertNotNull($this->category->getName());
    }

    /**
     * Test if url is not empty
     */
    public function testIfUrlIsNotEmpty()
    {
        $this->assertNotNull($this->category->getUrl());
    }

    /**
     * Test if url has correct format
     */
    public function testIfUrlHasProperFormat()
    {
        $this->assertEquals($this->category->getUrl(), 'https://www.example.com/desktops/monitors');
    }

    /**
     * Check if category has parent or not. If not return false.
     */
    public function testIfCategoryHasParent()
    {
        $this->assertNotNull($this->category->getParent());
    }

    /**
     * Check if category has no parent category
     */
    public function testIfCategoryHasNoParent()
    {
        $this->assertFalse($this->category->getParent());
    }

    /**
     * Check if there no breadcrumb set up
     */
    public function testIfCategoryHasNoBreadcrumb()
    {
        $this->category->setBreadcrumb([]);

        $this->assertEquals($this->category->getBreadcrumb(), []);
    }

    /**
     * Check if there is only one record for breadcrumb
     */
    public function testIfCategoryHasOneCategoryBreadcrumb()
    {
        $this->category->setBreadcrumb([
            'id' => 2,
            'name' => "Men's footwear",
            'parent' => false
        ]);

        $this->assertEquals($this->category->getBreadcrumb(), [
            'id' => 2,
            'name' => "Men's footwear",
            'parent' => false
        ]);
    }

    /**
     * Check if there is more the on record for breadcrumb
     */
    public function testIfCategoryHasTwoOreMoreCategoryBreadcrumb()
    {
        $this->assertEquals($this->category->getBreadcrumb(), [
            ["id" => 21, "name" => "Sneakers", "parent" => 20],
            ["id" => 20, "name" => "Shoes", "parent" => false]
        ]);
    }

    /**
     * Check if prepare category data return correct formed json
     */
    public function testIfCategoryPrepareDataHasProperFormat()
    {
        $this->assertEquals($this->category->prepareCategoryData(), json_encode([
            'id' => '89',
            'name' => 'Shoes',
            'url' => 'https://www.example.com/desktops/monitors',
            'parent' => false,
            'breadcrumb' => [
                ["id" => '21', "name" => "Sneakers", "parent" => 20],
                ["id" => '20', "name" => "Shoes", "parent" => false]
            ]
        ], JSON_PRETTY_PRINT));
    }
}