<?php
/**
 * Created by PhpStorm.
 * User: bratucornel
 * Date: 2019-02-22
 * Time: 10:25
 */

namespace RetargetingSDK;

use PHPUnit\Framework\TestCase;
use RetargetingSDK\Product;

/**
 * @property Product product
 */
class ProductTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        $this->product = new Product();

        $this->product->setUrl('http://www.google.ro');
        $this->product->setImg('https://www.google.com/img.png');
    }

    /**
     * Test if product has product id
     */
    public function test_if_product_has_id()
    {
        $this->product->setId(123);
        $this->assertNotNull($this->product->getId());
    }

    /**
     * Test if product has name
     */
    public function test_if_product_has_name()
    {
        $this->product->setName('Fooo');
        $this->assertNotNull($this->product->getName());
    }

    /**
     * Test if name is a string
     */
    public function test_if_product_name_is_string()
    {
        $this->product->setName('Galaxy Tab 10.0');
        $this->assertIsString($this->product->getName(), 'Galaxy Tab 11.0');
    }

    /**
     * Test if product url is set up
     */
    public function test_if_product_url_is_set()
    {
        $this->assertEquals($this->product->getUrl(), 'http://www.google.ro');
    }

    /**
     * Test if product has an image
     */
    public function test_if_product_has_image()
    {
        $this->assertEquals($this->product->getImg(), 'https://www.google.com/img.png');
    }

    /**
     * Test if product has price
     */
    public function test_if_product_has_price()
    {
        $this->product->setPrice(100.20);
        $this->assertNotNull($this->product->getPrice());
    }

    /**
     * Test product price when is float
     */
    public function test_when_product_price_is_float()
    {
        $this->product->setPrice('12.33');
        $this->assertEquals($this->product->getPrice(), 12.33);
    }

    /**
     * Test product price when is integer
     */
    public function test_when_product_price_is_int()
    {
        $this->product->setPrice('12');
        $this->assertEquals($this->product->getPrice(), 12);
    }

    public function test_when_product_promo_price_is_zero_and_promo_is_greater_than_price()
    {
        $this->product->setPrice(20);
        $this->product->setPromo(80);
        $this->assertEquals($this->product->getPromo(), 0);
    }

    /**
     * Test if product brand is array
     */
    public function test_if_product_brand_is_array()
    {
        $this->product->setBrand([
            'id' => 1,
            'name' => 'Apple'
        ]);

        $this->assertIsArray($this->product->getBrand());
    }

    /**
     * Test if product has brand
     */
    public function test_if_product_has_brand()
    {
        $this->product->setBrand([
            'id' => '1',
            'name' => 'Apple'
        ]);

        $this->assertEquals($this->product->getBrand(), ['id' => 1, 'name' => 'Apple']);
    }

    /**
     * Test if product category has correct format with only one parent category without subcategory and breadcrumb
     */
    public function test_if_product_category_has_correct_format_with_only_one_parent_category()
    {
        $this->product->setCategory([
            [
                "id" => 12,
                "name" => "Women footwear",
                "parent" => false,
                "breadcrumb" => []
            ]
        ]);

        $this->assertEquals($this->product->getCategory(), [
                "id" => 12,
                "name" => "Women footwear",
                "parent" => false,
                "breadcrumb" => []
        ]);
    }

    /**
     * Test if product has category with parent category, subcategory and breadcrumb
     */
    public function test_if_product_has_category_with_parent_category_and_breadcrumb()
    {
        $this->product->setCategory([
            [
                "id" => 75,
                "name" => "Men footwear",
                "parent" => false,
                "breadcrumb" => []
            ],
            [
                "id" => 22,
                "name" => "Sport sneakers",
                "parent" => 21,
                "breadcrumb" => [
                    ["id" => 21, "name" => "Sneakers", "parent" => 20],
                    ["id" => 20, "name" => "Shoes", "parent" => false]
                ]
            ]
        ]);

        $this->assertEquals($this->product->getCategory(), [
            [
                "id" => 75,
                "name" => "Men footwear",
                "parent" => false,
                "breadcrumb" => []
            ],
            [
                "id" => 22,
                "name" => "Sport sneakers",
                "parent" => 21,
                "breadcrumb" => [
                    ["id" => 21, "name" => "Sneakers", "parent" => 20],
                    ["id" => 20, "name" => "Shoes", "parent" => false]
                ]
            ]
        ]);
    }

    /**
     * Test if product inventory is array
     */
    public function test_if_product_inventory_is_array()
    {
        $this->product->setInventory([
            "variations" => true,
            "stock" => [
                "42-B" => true,
                "42-W" => false,
                "43-B" => true,
                "43-W" => true
            ]
        ]);

        $this->assertIsArray($this->product->getInventory());
    }

    /**
     * Test if product inventory is array
     */
    public function test_if_product_inventory_has_correct_format()
    {
        $this->product->setInventory([
            "variations" => true,
            "stock" => [
                "42-B" => true,
                "42-W" => false,
                "43-B" => true,
                "43-W" => true
            ]
        ]);

        $this->assertEquals($this->product->getInventory(), [
            "variations" => true,
            "stock" => [
                "42-B" => true,
                "42-W" => false,
                "43-B" => true,
                "43-W" => true
            ]
        ]);
    }

    /**
     * Test if product additional images is not null
     */
    public function test_if_product_has_additional_images()
    {
        $this->product->setAdditionalImages([
            'https://www.example.com/image/product-test-1.png',
            'https://www.example.com/image/product-test-2.png',
            'https://www.example.com/image/product-test-3.png',
            'https://www.example.com/image/product-test-4.png',
        ]);

        $this->assertNotNull($this->product->getAdditionalImages());
    }

    /**
     * Check if images url have proper format and data is returned correctly
     */
    public function test_if_product_additional_images_return_correct_format_array()
    {
        $this->product->setAdditionalImages([
            'https://www.example.com/image/product-test-1.png',
            'https://www.example.com/image/product-test-2.png',
            'https://www.example.com/image/product-test-3.png',
            'https://www.example.com/image/product-test-4.png',
        ]);

        $this->assertEquals($this->product->getAdditionalImages(), [
            'https://www.example.com/image/product-test-1.png',
            'https://www.example.com/image/product-test-2.png',
            'https://www.example.com/image/product-test-3.png',
            'https://www.example.com/image/product-test-4.png',
        ]);
    }

    /**
     * Check product prepare information returns correct array format
     */
    public function test_if_product_prepare_information_return_correct_format_array()
    {
        $brand = [
            'id' => "8",
            'name' => 'Apple'
        ];

        $category = [
            [
                "id" => "20",
                "name" => "Desktops",
                "parent" => false,
                "breadcrumb" => []
            ],
            [
                "id" => "28",
                "name" => "Monitors",
                "parent" => "25",
                "breadcrumb" => [
                    ["id" => "25", "name" => "Components", "parent" => false]
                ]
            ]
        ];

        $inventory = [
            'variations' => true,
            'stock' => [
                "Small" => true,
                "Medium" => true,
                "Large" => true,
                "Checkbox 1" => true,
                "Checkbox 2" => true,
                "Checkbox 3" => true,
                "Checkbox 4" => true,
                "Red" => true,
                "Blue" => true,
                "Green" => true,
                "Yellow" => true
            ]
        ];

        $additionalImages = [
            "http://localhost/upload/image/catalog/demo/canon_logo.jpg",
            "http://localhost/upload/image/catalog/demo/hp_1.jpg",
            "http://localhost/upload/image/catalog/demo/compaq_presario.jpg",
            "http://localhost/upload/image/catalog/demo/canon_eos_5d_1.jpg",
            "http://localhost/upload/image/catalog/demo/canon_eos_5d_2.jpg"
        ];

        $product = new Product();
        $product->setId(42);
        $product->setName('Apple Cinema 30"');
        $product->setUrl('http://localhost/upload/test');
        $product->setImg('http://localhost/upload/image/catalog/demo/apple_cinema_30.jpg');
        $product->setPrice(122);
        $product->setPromo(90);
        $product->setBrand($brand);
        $product->setCategory($category);
        $product->setInventory($inventory);
        $product->setAdditionalImages($additionalImages);

        $result = json_encode([
                "id" => 42,
                "name" => "Apple Cinema 30\"",
                "url" => "http://localhost/upload/test",
                "img" => "http://localhost/upload/image/catalog/demo/apple_cinema_30.jpg",
                "price" => 122,
                "promo" => 90,
                "brand" => [
                    "id" => "8",
                    "name" => "Apple"
                ],
                "category" => [
                    [
                        "id" => "20",
                        "name" => "Desktops",
                        "parent" => false,
                        "breadcrumb" => []
                    ],
                    [
                        "id" => "28",
                        "name" => "Monitors",
                        "parent" => "25",
                        "breadcrumb" => [
                            [
                                "id" => "25",
                                "name" => "Components",
                                "parent" => false
                            ]
                        ]
                    ]
                ],
                "inventory" => [
                    "variations" => true,
                    "stock" => [
                        "Small" => true,
                        "Medium" => true,
                        "Large" => true,
                        "Checkbox 1" => true,
                        "Checkbox 2" => true,
                        "Checkbox 3" => true,
                        "Checkbox 4" => true,
                        "Red" => true,
                        "Blue" => true,
                        "Green" => true,
                        "Yellow" => true
                    ]
                ],
                "images" => [
                    "http://localhost/upload/image/catalog/demo/canon_logo.jpg",
                    "http://localhost/upload/image/catalog/demo/hp_1.jpg",
                    "http://localhost/upload/image/catalog/demo/compaq_presario.jpg",
                    "http://localhost/upload/image/catalog/demo/canon_eos_5d_1.jpg",
                    "http://localhost/upload/image/catalog/demo/canon_eos_5d_2.jpg"
                ]
            ], JSON_PRETTY_PRINT);

        $this->assertEquals($product->prepareProductInformationToJson(), $result);
    }
}

