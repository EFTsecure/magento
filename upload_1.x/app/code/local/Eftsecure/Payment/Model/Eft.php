<?php
/**
 *
 */

/**
 * Class Eftsecure_Payment_Model_Eft
 */
class Eftsecure_Payment_Model_Eft
    extends Mage_Payment_Model_Method_Abstract
{
    const PAYMENT_METHOD_EFTSECURE_CODE = 'eftsecure_payment';

    /**
     * Payment method code
     *
     * @var string
     */
    protected $_code = self::PAYMENT_METHOD_EFTSECURE_CODE;


    protected $_isGateway = true;

    protected $_isInitializeNeeded = true;

    /**
     * Bank Transfer payment block paths
     *
     * @var string
     */
    protected $_formBlockType = 'eftsecure_payment/form_eft';
    protected $_infoBlockType = 'eftsecure_payment/info_eft';

    /**
     * Method that will be executed instead of authorize or capture
     * if flag isInitializeNeeded set to true
     *
     * @param string $paymentAction
     * @param object $stateObject
     *
     * @return Eftsecure_Payment_Model_Eft
     */
    public function initialize($paymentAction, $stateObject)
    {
        try {

            /** @var Mage_Core_Helper_Data $helper */
            $helper = Mage::helper('core');

            /** @var Eftsecure_Payment_Model_Callpay $callPay */
            $callPay = Mage::getModel('eftsecure_payment/callpay');

            $jsonToken = $callPay->getToken(
                $this->getConfigData('api_username'),
                $helper->decrypt($this->getConfigData('api_password')),
                true
            );

            $encryptedToken = $helper->encrypt($jsonToken);

            /** @var Mage_Sales_Model_Order_Payment $payment */
            $payment = $this->getInfoInstance();

            $payment->setAdditionalInformation(
                'eftsecure_token',
                $encryptedToken
            );

        } catch(Eftsecure_Payment_Exception $e) {

            throw new Mage_Core_Exception(
                $this->_getHelper()->__(
                    "%s payment method is currently not available. Please use ".
                    "a different payment method or try again later. ",
                    $this->getConfigData('title')
                )
            );

        }

        return $this;
    }

    public function getOrderPlaceRedirectUrl()
    {
        Mage::getSingleton('checkout/session')->setEftsecureFlag(1);

        return Mage::getUrl('eftsecure_payment/redirect');
    }

    /**
     * Get instructions text from config
     *
     * @return string
     */
    public function getInstructions()
    {
        return trim($this->getConfigData('instructions'));
    }
}
