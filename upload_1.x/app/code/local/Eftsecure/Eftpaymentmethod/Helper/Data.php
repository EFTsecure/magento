<?php
class Eftsecure_Eftpaymentmethod_Helper_Data extends Mage_Core_Helper_Abstract
{
  function getPaymentGatewayUrl() 
  {
    return Mage::getUrl('eftpaymentmethod/payment/gateway', array('_secure' => false));
  }
}