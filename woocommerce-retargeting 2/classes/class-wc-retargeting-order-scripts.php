<?php
/**
 * Order Scripts
 */

if (!defined('ABSPATH')) {
    exit;
}
 class WC_Retargeting_Order_Scripts {
    
    private $coupons_list;

    private function get_retargeting_coupon($order, $order_id) {
        $coupons_list = '';
        if ($order->get_used_coupons()) {
            $coupons_count = count($order->get_used_coupons());
            $i = 1;
            foreach ($order->get_used_coupons() as $coupon) {
                $coupons_list .= $coupon;
                if ($i < $coupons_count) {
                    $coupons_list .= ', ';
                    $i++;
                }
            }
            
        }
        return $coupons_list;
    }

    private function get_retargeting_items($order_id, $order, $data) {
        foreach ((array)$order->get_items() as $item_id => $item) {
            $_product = apply_filters('woocommerce_order_item_product', $order->get_product_from_item($item), $item);
            $item_meta = new WC_Order_Item_Meta($item['item_meta'], $_product);
            if (apply_filters('woocommerce_order_item_visible', true, $item)) {
                $line_item = array(
                    'id' => $item['product_id'],
                    'name' => $item['name'],
                    'price' => $item['line_subtotal'],
                    'quantity' => $item['qty'],
                    'variation_code' => ($item['variation_id'] == 0) ? "" : $item['variation_id']
                );
            }
            $data['line_items'][] = $line_item;
        }
        return $data;
    }

    private function get_retargeting_order_script($order, $data) {
        $orderScript = '<script>
                var _ra = _ra || {};
                _ra.saveOrderInfo = {
                    "order_no": ' . $order->get_id() . ',
                    "lastname": "' . $order->get_billing_last_name() . '",
                    "firstname": "' . $order->get_billing_first_name() . '",
                    "email": "' . $order->get_billing_email() . '",
                    "phone": "' . $order->get_billing_phone() . '",
                    "state": "' . $order->get_billing_state() . '",
                    "city": "' . $order->get_billing_city() . '",
                    "address": "' . $order->get_billing_address_1() . " " . $order->get_billing_address_2() . '",
                    "discount_code": "' . $this->coupons_list . '",
                    "discount": ' . (empty($order->get_discount) ? 0 : $order->get_discount) . ',
                    "shipping": ' . (empty($order->get_total_shipping) ? 0 : $order->get_total_shipping) . ',
                    "rebates": 0,
                    "fees": 0,
                    "total": ' . $order->get_total() . '
                };
                _ra.saveOrderProducts =
                    ' . json_encode($data['line_items']) . '
                ;
                
                if( _ra.ready !== undefined ){
                    _ra.saveOrder(_ra.saveOrderInfo, _ra.saveOrderProducts);
                }
            </script>';
            return $orderScript;
    }

    private function get_retargeting_order_info($order, $object, $data) {
        $orderInfo = array(
            "order_no" => $order->get_id(),
            "lastname" => $order->get_billing_last_name(),
            "firstname" => $order->get_billing_first_name(),
            "email" => $order->get_billing_email(),
            "phone" => $order->get_billing_phone(),
            "state" => $order->get_billing_state(),
            "city" => $order->get_billing_city(),
            "address" => $order->get_billing_address_1() . " " . $order->get_billing_address_2(),
            "discount_code" => $this->coupons_list,
            "discount" => (empty($order->get_discount) ? 0 : $order->get_discount),
            "shipping" => (empty($order->get_total_shipping) ? 0 : $order->get_total_shipping),
            "total" => $order->get_total()
        );

        if ($object->token && $object->token != '') {

            $orderClient = new Retargeting_REST_API_Client($object->token);
            $orderClient->setResponseFormat("json");
            $orderClient->setDecoding(false);
            $response = $orderClient->order->save($orderInfo, $data['line_items']);

        }
    }

     public function save_retargeting_order($order_id, $object) {
        $order = new WC_Order($order_id);
        if (is_numeric($order_id) && $order_id > 0) {
            $this->coupons_list = $this->get_retargeting_coupon($order, $order_id);
            $data = array('line_items' => array(),);
            $data = $this->get_retargeting_items($order_id, $order, $data);
            $script = $this->get_retargeting_order_script($order, $data);
            echo $script;
        }
        $info = $this->get_retargeting_order_info($order, $object, $data);
        return $info;
     }
 }
?>