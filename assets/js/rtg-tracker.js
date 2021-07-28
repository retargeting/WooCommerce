/**
 * 2014-2020 Retargeting BIZ SRL
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@retargeting.biz so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    Retargeting SRL <info@retargeting.biz>
 * @copyright 2014-2020 Retargeting SRL
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
// (function ()
// {
//     'use strict';
//
//     jQuery( document ).ajaxSuccess(function( event, xhr, settings )
//     {
//         if (settings.url.indexOf('wc-ajax=add_to_cart') >= 0)
//         {
//             var dataOb = {};
//
//             settings.data.replace(/([^=&]+)=([^&]*)/g, function(m, key, value)
//             {
//                 dataOb[decodeURIComponent(key)] = decodeURIComponent(value);
//             });
//
//             if(dataOb.hasOwnProperty('product_id'))
//             {
//                 if(typeof _ra === "undefined")
//                 {
//                     _ra = {};
//                 }
//
//                 _ra.addToCartInfo = {
//                     "product_id": dataOb.product_id,
//                     "quantity"  : dataOb.hasOwnProperty('quantity') ? dataOb.quantity : '1',
//                     "variation" : false
//                 };
//
//                 if (_ra.ready !== undefined)
//                 {
//                     _ra.addToCart(
//                         _ra.addToCartInfo.product_id,
//                         _ra.addToCartInfo.quantity,
//                         _ra.addToCartInfo.variation
//                     );
//                 }
//             }
//         }
//     });
// })();

/*
_ra.removeFromCartInfo = {
    "product_id": dataOb.id_product,
    "quantity"  : dataOb.hasOwnProperty('qty') ? dataOb.qty : '1',
    "variation" : false
};

if (_ra.ready !== undefined)
{
    _ra.removeFromCart(
        _ra.removeFromCartInfo.product_id,
        _ra.removeFromCartInfo.quantity,
        _ra.removeFromCartInfo.variation
    );
}*/
