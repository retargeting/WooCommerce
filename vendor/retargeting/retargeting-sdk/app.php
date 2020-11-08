<?php
/**
 * Created by PhpStorm.
 * User: bratucornel
 * Date: 2019-03-13
 * Time: 12:46
 */

require_once 'vendor/autoload.php';

$test = [
        "firstName" => "Jane",
      "lastName" => "Doe",
      "email" =>  "jane.doe@example.com",
      "phone" => "",
      "status" => false
];

$data = json_encode($test);

$enc = \Retargeting\Helpers\Encryption::encrypt($data);

var_dump($enc);


//try {
//    $dec = (new Retargeting\Helpers\Decryption())->decrypt($enc);
//} catch (\Retargeting\Exceptions\DecryptException $e) {
//}
//
//var_dump($dec);
//
//
//var_dump(\Retargeting\Helpers\Token::createRandomToken());