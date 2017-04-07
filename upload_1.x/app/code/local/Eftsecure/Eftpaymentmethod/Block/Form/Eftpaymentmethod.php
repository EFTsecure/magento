<?php
class Eftsecure_Eftpaymentmethod_Block_Form_Eftpaymentmethod extends Mage_Payment_Block_Form
{
  protected function _construct()
  {
    parent::_construct();
    $this->setTemplate('Eftpaymentmethod/form/info.phtml');
  }
}