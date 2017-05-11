<?php


class Eftsecure_Payment_RedirectController
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

    protected function _cancelOrder()
    {
        $session = $this->_getCheckout();

        if ($quoteId = $session->getQuoteId()) {

            /** @var Mage_Sales_Model_Quote $quote */
            $quote = Mage::getModel('sales/quote');

            $quote->load($quoteId);

            if ($quote->getId()) {
                $quote->setIsActive(true)->save();
            }
        }

        // Cancel order
        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::getModel('sales/order');

        $order->loadByIncrementId($session->getLastRealOrderId());

        if ($order->getId()) {
            $order->cancel()->save();
        }
    }

    /**
     * Retrieve information from payment configuration
     *
     * @param string $field
     * @param int|string|null|Mage_Core_Model_Store $storeId
     *
     * @return mixed
     */
    protected function _getConfigData($field, $storeId = null)
    {
        $path = 'payment/eftsecure_payment/'.$field;
        return Mage::getStoreConfig($path, $storeId);
    }

    public function indexAction()
    {
        try {

            $session = $this->_getCheckout();

            /** @var Mage_Sales_Model_Order $order */
            $order = Mage::getModel('sales/order');
            $order->loadByIncrementId($session->getLastRealOrderId());

            if (!$order->getId()) {
                Mage::throwException('No matching order was found');
            }

            /** @var Mage_Sales_Model_Order_Payment $payment */
            $payment = $order->getPayment();

            if (!$session->getEftsecureFlag() || $payment->getMethod() != 'eftsecure_payment') {
                Mage::throwException('Incorrect Payment method found for matching order');
            }

            if ($session->getLastSuccessQuoteId()) {
                $session->setTempLastSuccessQuoteId($session->getLastSuccessQuoteId());
                $session->setLastSuccessQuoteId(null);
            }

            $this->getResponse()->setBody(
                $this->getLayout()->createBlock('eftsecure_payment/redirect')->toHtml()
            );

            return;

        } catch (Exception $e) {

            $this->_getCheckout()->addError($e->getMessage());
            $this->_redirect('checkout/onepage/failure');
            return;
        }
    }

    public function successAction()
    {
        $success        = $this->getRequest()->getParam('success');
        $incrementId    = $this->getRequest()->getParam('merchant_reference');
        $reference      = $this->getRequest()->getParam('gateway_reference');
        $organisationId = $this->getRequest()->getParam('organisation_id');
        $status         = $this->getRequest()->getParam('status');

        try {

            if ($success != 'true' && $status != 'success') {
                Mage::throwException('Unsuccessful Payment');
            }

            /** @var Mage_Sales_Model_Order $order */
            $order = Mage::getModel('sales/order');
            $order->loadByIncrementId($incrementId);

            if (!$order->getId()) {
                Mage::throwException('Order not found');
            }

            if (!$order->canInvoice()) {
                $this->_redirect('/');
                return;
            }

            if ($order->getState() !== 'new') {
                // Mage::getSingleton('core/session')->addNotice('?');
                $this->_redirect('sales/order/view', array('order_id' => $order->getId()));
                return;
            }

            /** @var Mage_Core_Helper_Data $helper */
            $helper = Mage::helper('core');

            /** @var Eftsecure_Payment_Model_Callpay $callPay */
            $callPay = Mage::getModel('eftsecure_payment/callpay');

            $arrToken = $callPay->getToken(
                $this->_getConfigData('api_username'),
                $helper->decrypt($this->_getConfigData('api_password'))
            );

            if ($organisationId != $arrToken['organisation_id']) {
                Mage::throwException('Unsuccessful Payment: Mismatch Organisation');
            }

            $validation = $callPay->retrieveTransaction($arrToken['token'], $reference);

            if (!isset($validation['successful']) || !$validation['successful']) {
                Mage::throwException('Unsuccessful Payment: Invalid Transaction');
            }


            if ($validation['merchant_reference'] != $order->getIncrementId()) {
                Mage::throwException("Unsuccessful Payment: Invalid order Id");
            }

            if (number_format($order->getGrandTotal(), 2) != $validation['amount']) {
                Mage::throwException("Unsuccessful Payment: Incorrect payment amount");
            }

            /** @var Mage_Sales_Model_Order_Payment $payment */
            $payment = $order->getPayment();

            $payment->unsAdditionalInformation('eftsecure_token');
            $payment->setAdditionalInformation(
                'eftsecure',
                $helper->jsonEncode(array(
                    'transaction_id'    => $reference,
                    'gateway_reference' => $validation['gateway_reference'],
                    'service'           => $validation['service'],
                    'gateway'           => $validation['gateway'],
                    'name'              => $validation['gateway_response_parameters']['customer']['name'],
                    'bank'              => $validation['gateway_response_parameters']['customer']['bank'],
                    'branch_code'       => $validation['gateway_response_parameters']['customer']['branch_code'],
                    'account_type'      => $validation['gateway_response_parameters']['customer']['account_type'],
                    'account_num'       => $validation['gateway_response_parameters']['customer']['account']
                ))
            );

            /** @var Mage_Sales_Model_Order_Invoice $invoice */
            $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();

            $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
            $invoice->setState(Mage_Sales_Model_Order_Invoice::STATE_PAID);
            $invoice->addComment('Invoice generated automatically for Eftsecure Payment');
            $invoice->register();

            $order->setState(
                Mage_Sales_Model_Order::STATE_PROCESSING,
                $this->_getConfigData('payed_status'),
                'Order payed via Eftsecure'
            );

            $transactionSave = Mage::getModel('core/resource_transaction')
                ->addObject($invoice)
                ->addObject($order);

            $transactionSave->save();

            $session = $this->_getCheckout();

            if ($session->getLastRealOrderId() != $order->getIncrementId()) {
                Mage::getSingleton('core/session')->addNotice('Payment successfully made');
                $this->_redirect('sales/order/view', array('order_id' => $order->getId()));
                return;
            }

            $session->setLastSuccessQuoteId($session->getTempLastSuccessQuoteId());

            $this->_redirect('checkout/onepage/success', array('_secure' => true));
            return;

        } catch (Exception $e) {

            $session = $this->_getCheckout();

            if ($session->getLastRealOrderId() != $incrementId) {
                // Assuming the user is trying something funny
                $this->_redirect('/');
            }

            $this->_getCheckout()->addError(
                'There was a error while processing your payment.' .
                'Please try again'
            );

            $this->_cancelOrder();

            $this->_redirect('checkout');

            return;
        }
        
        return;
    }

    public function cancelAction()
    {
        Mage::log($_REQUEST);
        return;
    }

    public function errorAction()
    {
        Mage::log($_REQUEST);
        return;
    }
}
