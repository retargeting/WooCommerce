<?php
/**
 * Email Scripts
 */
if (!defined('ABSPATH')) {
    exit;
}
 class WC_Retargeting_Email_Scripts {

     public function set_retargeting_email() {
        global $woocommerce;
        $email = array();
        $email['email'] = wp_get_current_user()->user_email;
        if ((!isset($_SESSION['set_email']) || $_SESSION['set_email'] != $email['email']) && (!empty($email['email']))) {
            $emailScript = '
            <script type="text/javascript">
                var _ra = _ra || {};

                _ra.setEmailInfo = {
                    "email": "' . $email['email'] . '"
                };

                if(_ra.ready !== undefined) {
                    _ra.setEmail(_ra.setEmailInfo)
                }
            </script>';
            $_SESSION['set_email'] = $email['email'];
            return $emailScript;
        }
     }
 }
?>