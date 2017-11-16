<?php


class Eftsecure_Payment_NotifyController
    extends Mage_Core_Controller_Front_Action
{
    /**
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckout()
    {
        /** @var Mage_Checkout_Model_Session $session */
        $session = Mage::getSingleton('checkout/session');
        return $session;
    }

    public function indexAction()
    {
        Mage::log($_REQUEST);
    }
}
