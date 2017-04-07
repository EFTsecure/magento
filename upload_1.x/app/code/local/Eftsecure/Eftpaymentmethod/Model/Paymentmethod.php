<?php
class Eftsecure_Eftpaymentmethod_Model_Paymentmethod extends Mage_Payment_Model_Method_Abstract {
  protected $_code  = 'eftpaymentmethod';
  protected $_formBlockType = 'eftpaymentmethod/form_eftpaymentmethod';
  //protected $_infoBlockType = 'payment/info';
 
  public function getOrderPlaceRedirectUrl()
  {
	Mage::getSingleton('core/session')->setEftsecureFlag(1);
    return Mage::getUrl('eftpaymentmethod/payment/redirect', array('_secure' => false));
  }
}