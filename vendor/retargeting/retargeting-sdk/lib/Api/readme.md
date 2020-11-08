# REST API Response examples

## Customers

Each customer object must be encrypted using Encryption Helpers. When you develop this API Endpoint, you can leave it plain.

Response example (plain)

```json

{
  "data" : [
    {
      "firstName": "John",
      "lastName": "Doe",
      "email": "john.doe@example.com",
      "phone": "0770123456",
      "status": true
    },
    {
      "firstName": "Jane",
      "lastName": "Doe",
      "email": "jane.doe@example.com",
      "phone": "",
      "status": false
    }
  ],
  "current_page": 1,
  "last_page": 145,
  "next_page": "/retargetingtracker/customers?page=2",
  "prev_page": "/retargetingtracker/customers?page=1"
}

```

Response example (encrypted)

```json

{
    "data": [
      "CLgwGLmYEkecnT5ZIEjfxCTWmmCs-WWOJW7uft-fkINjFDYLB4F9RQ42gqw1yfsnWumg6I2XlXQ2oCO3b29pFY7f5-2XceCXsAto51RMA2alKFIPgMz59Tv0iYSppw7BETH-9PnEv3eS8ZCNseaPcKiL6RQNcpyJ2kSpTLBJFdk",
      "Js35cJP-vCLMi9dXYLffUMgle9m9hV8VBeVfSNNM1qiumHSmhyt3cQGR3cIKraNijP3sm4GtkXDr0XBMC29G9k05oGhtfPmEoYAFEjhGwikEHMsWCWo1luew7rpEeEh2VTT9JKmsz_z-eowe97TRPw"
    ],
    "current_page": 1,
    "last_page": 145,
    "next_page": "/retargetingtracker/customers?page=2",
    "prev_page": "/retargetingtracker/customers?page=1"
}

```

## Stock Management

Call api stock management each time you make some changes on your products. 

Request example

```php
use Retargeting/Api;

$stock = new StockManagement();

$stock->setProductId(123);
$stock->setName('Canon EOS 5D');
$stock->setPrice(900);
$stock->setPromo(800);
$stock->setImage('https://www.example.com/products/image/canon-eos-5d.jpg');
$stock->setUrl('https://www.example.com/products/camera/canon-eos-5d');
$stock->setStock(true);

$api_key = 'YOUR_RTG_GENERATED_API_KEY';
$product_data = $stock->prepareStockInfo();

//Update stock each time you make some change
$stock->updateStock($api_key, $product_data);
```
