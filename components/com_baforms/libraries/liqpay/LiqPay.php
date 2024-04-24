<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

class LiqPay
{
    private $_public_key;
    private $_private_key;

    public function __construct($public_key, $private_key)
    {
        $this->_public_key = $public_key;
        $this->_private_key = $private_key;
    }

    public function cnb_form($params)
    {
        $params = $this->cnb_params($params);
        $data = $this->encode_params($params);
        $signature = $this->cnb_signature($params);
        
        return sprintf('
            <form id="payment-form" method="POST" action="https://www.liqpay.ua/api/3/checkout" accept-charset="utf-8">
                %s
                %s
            </form>
            <script type="text/javascript">
                document.getElementById("payment-form").submit();
            </script>
            ',
            sprintf('<input type="hidden" name="%s" value="%s" />', 'data', $data),
            sprintf('<input type="hidden" name="%s" value="%s" />', 'signature', $signature)
        );
    }

    public function cnb_signature($params)
    {
        $private_key = $this->_private_key;
        $json = $this->encode_params($params);
        $signature = $this->str_to_sign($private_key.$json.$private_key);

        return $signature;
    }

    private function cnb_params($params)
    {
        $params['public_key'] = $this->_public_key;

        return $params;
    }

    private function encode_params($params)
    {
        return base64_encode(json_encode($params));
    }

    public function str_to_sign($str)
    {
        $signature = base64_encode(sha1($str, 1));

        return $signature;
    }
}