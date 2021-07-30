# Retargeting SDK 

## Overview
Retargeting SDK is a software development tool for E-Commerce platforms that simplifies the implementation of Retargeting extension.

## Minimum requirements
The Retargeting SDK requires at least PHP version 5.4.0 and it's also compatible with PHP >= 7.0.0.

## How to install
Clone the repository in your platform root folder.

## Example

### Product class for sendProduct implementation

#### Sample request
```php
use Retargeting/Product;

$brand = [
     'id' => 8, 
     'name' => 'Apple'
];

$category = [
    [
      "id" => 20,
      "name" => "Desktop",
      "parent" => false,
      "breadcrumb" => []
    ],
    [
      "id" => 28,
      "name" => "Monitors",
      "parent" => 25,
      "breadcrumb" => [
          ["id" => 25, "name" => "Components", "parent" => false]     
    ]
];

$inventory = [
    'variations' => true,
    'stock' => [
        'Red' => true,
        'Small' => false,
        'Medium' => true,
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
$product->setName('Shoes');
$product->setUrl('http://localhost/upload/test');
$product->setImg('http://localhost/upload/image/catalog/demo/apple_cinema_30.jpg');
$product->setPrice(122);
$product->setPromo(90);
$product->setBrand($brand);
$product->setCategory($category);
$product->setInventory($inventory);
$product->setAdditionalImages($additionalImages)

echo $product->getData();
```

#### Sample response
```json
[
    {
        "id": 42,
        "name": "Apple Cinema 30\"",
        "url": "http://localhost/upload/test",
        "img": "http://localhost/upload/image/catalog/demo/apple_cinema_30.jpg",
        "price": 122,
        "promo": 90,
        "brand": {
            "id": "8",
            "name": "Apple"
        },
        "category": [
            {
                "id": "20",
                "name": "Desktops",
                "parent": false,
                "breadcrumb": []
            },
            {
                "id": "28",
                "name": "Monitors",
                "parent": "25",
                "breadcrumb": [
                    {
                        "id": "25",
                        "name": "Components",
                        "parent": false
                    }
                ]
            }
        ],
        "inventory": {
            "variations": true,
            "stock": {
                "Small": true,
                "Medium": true,
                "Large": true,
                "Checkbox 1": true,
                "Checkbox 2": true,
                "Checkbox 3": true,
                "Checkbox 4": true,
                "Red": true,
                "Blue": true,
                "Green": true,
                "Yellow": true
            }
        },
        "images": [
            "http://localhost/upload/image/catalog/demo/canon_logo.jpg",
            "http://localhost/upload/image/catalog/demo/hp_1.jpg",
            "http://localhost/upload/image/catalog/demo/compaq_presario.jpg",
            "http://localhost/upload/image/catalog/demo/canon_eos_5d_1.jpg",
            "http://localhost/upload/image/catalog/demo/canon_eos_5d_2.jpg"
        ]
    }
]
```

|    **Method**    |    **Type**    |    **Required**    |    **Description**    |
|---|---|---|---|
|  setId  |  Number or text  |  Required  |  The product item identifier, ie. itemcode. It should identify to the sold product, but not necessarily some specific variant of the product. Must be unique in your site.  |
|	setName	|	Text	|	Required	|	The product name	|
|	setUrl	|	URL	|	Required	|	Complete URL of the item. Must start with http:// or https://.	|
|	setImg	|	URL	|	Required	|	Complete URL of an image of the item.	|
|	setPrice	|	Number or text	|	Required	|	Current product price. If the product is on promotion (price is reduced) then this parameter gets the value of the price before promotion was applied to the product (old price).	|
|	setPromo	|	Number or text	|	Optional	|	Promotional price (new price). When the product isn’t on promotion (no reduced price), send value 0.	|	|
|	setBrand	|	Object	|	Required	|	Details about product brand. If the product does not belong to any brand, send false value. The object containing brand details, has the following properties: id, name.	|
|	brand.id	|	Number or text	|	Required	|	The brand item identifier.	|
|	brand.name	|	Text	|	Required	|	Brand name	|
|	setCategory	|	Object	|	Required	|	An object that contain details about products category. The object should contain the following properties: id, name, parent	|
|	category.id	|	Number or text	|	Required	|	The category identifier	|
|	category.name	|	Text	|	Required	|	Category name	|
|	category.parent	|	Number, text, false	|	Required	|	Id of parent category. If there isn’t any parent category, send false value.	|
|	setBreadcrumb	|	Array	|	Required	|	Array containing all the parent categories of the category to which the product belongs (in this array you must not add the product category). If the category does not have a parent category (category.parent is set false), send an empty array. Each parent category is sent as object and contains the following properties: id, name, parent.	|
|	breadcrumb.id	|	Number or text	|	Required	|	Category id	|
|	breadcrumb.name	|	Text	|	Required	|	Category Name	|
|	breadcrumb.parent	|	Number, text, false	|	Required	|	Id of parent category. If there isn’t any parent category, send false value.	|
|	setInventory	|	Object	|	Required	|	Inventory details	|
|	inventory.variations	|	True/False	|	Required	|	True for products with variations. False for products without variations.	|
|	inventory.stock	|	True/False/Object	|	Required	|	For product with variations, you should send an object with stock for each variations.	|
|	setAdditionalImages	|	Object	|	Required	|	All product images can be assigned here. Accepts an object of urls.
|	callback_function	|	Function	|	Optional	|	With this parameter you can define a function that runs itself after the action’s parent function executes	|
