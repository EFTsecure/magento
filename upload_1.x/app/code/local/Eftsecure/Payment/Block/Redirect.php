<?php
/**
 *
 */

/**
 * Class Eftsecure_Payment_Block_Redirect
 */
class Eftsecure_Payment_Block_Redirect extends Mage_Payment_Block_Form
{
    /**
     * Order object
     *
     * @var Mage_Sales_Model_Order
     */
    protected $_order;

    /**
     * Payment Object
     *
     * @var Mage_Sales_Model_Order_Payment
     */
    protected $_payment;

    /**
     * Eftsecure Token
     *
     * @var array
     */
    protected $_arrToken;

    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('eftsecure_payment/redirect.phtml');
    }

    /**
     * Get frontend checkout session object
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Get order object
     *
     * @return Mage_Sales_Model_Order
     */
    protected function _getOrder()
    {
        if (!$this->_order) {
            $incrementId = $this->_getCheckout()->getLastRealOrderId();
            $this->_order = Mage::getModel('sales/order')
                ->loadByIncrementId($incrementId);
        }
        return $this->_order;
    }

    /**
     * Get Payment Object
     *
     * @return Mage_Sales_Model_Order_Payment
     */
    protected function _getPayment()
    {
        if (!$this->_payment) {
            $this->_payment = $this->_getOrder()->getPayment();
        }
        return $this->_payment;
    }

    /**
     * Get Token
     *
     * @param null|string $key Possible keys: expires, token, organisation_id
     *
     * @return array|string
     */
    protected function _getToken($key = null) {

        if (!$this->_arrToken) {
            $token = $this->_getPayment()
                ->getAdditionalInformation('eftsecure_token');

            /** @var Mage_Core_Helper_Data $helper */
            $helper = Mage::helper('core');

            $this->_arrToken = $helper->jsonDecode($helper->decrypt($token));
        }

        if (!is_null($key) && isset($this->_arrToken[$key])) {
            return $this->_arrToken[$key];
        }

        return $this->_arrToken;
    }

    protected function _getSuccessUrl()
    {
        return Mage::getUrl(
            'eftsecure_payment/redirect/success', array('_secure' => true)
        );
    }

    protected function _getCanceledUrl()
    {
        return Mage::getUrl(
            'eftsecure_payment/redirect/cancel', array('_secure' => true)
        );
    }

    protected function _getErrorUrl()
    {
        return Mage::getUrl(
            'eftsecure_payment/redirect/error', array('_secure' => true)
        );
    }

    protected function _getNotifyUrl()
    {
        return Mage::getUrl(
            'eftsecure_payment/notify', array('_secure' => true)
        );
    }
}