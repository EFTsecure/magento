<?php


class Eftsecure_Payment_Block_Info_Eft
    extends Mage_Payment_Block_Info
{
    /**
     * Instructions text
     *
     * @var string
     */
    protected $_instructions;

    /**
     * _prepareSpecificInformation function.
     *
     * @access protected
     * @param mixed $transport (default: null)
     * @return void
     */
    protected function _prepareSpecificInformation($transport = null)
    {
        if (null !== $this->_paymentSpecificInformation) {
            return $this->_paymentSpecificInformation;
        }

        /** @var Mage_Core_Helper_Data $helper */
        $helper = Mage::helper('core');

        /** @var Mage_Sales_Model_Order_Payment $payment */
        $payment = $this->getInfo();

        $transport = new Varien_Object();
        $transport = parent::_prepareSpecificInformation($transport);

        $eftsecureInfo = $helper->jsonDecode(
            $payment->getAdditionalInformation('eftsecure')
        );

        $transport->addData(array(
            'EFTsecure Transaction ID' => $eftsecureInfo['transaction_id'],
            'Service'                  => $eftsecureInfo['service'],
            'Gateway'                  => $eftsecureInfo['gateway'],
            'Gateway Reference #'      => $eftsecureInfo['gateway_reference'],
            'Customer Name'            => $eftsecureInfo['name'],
            'Bank'                     => $eftsecureInfo['bank'],
            'Branch Code'              => $eftsecureInfo['branch_code'],
            'Account Type'             => $eftsecureInfo['account_type'],
            'Account #'                => $eftsecureInfo['account_num']
        ));

        $this->_paymentSpecificInformation = $transport;

        return $this->_paymentSpecificInformation;
    }

    /**
     * Get instructions text from order payment
     * (or from config, if instructions are missed in payment)
     *
     * @return string
     */
    public function getInstructions()
    {
        if (is_null($this->_instructions)) {
            $this->_instructions = $this->getInfo()->getAdditionalInformation('instructions');
            if(empty($this->_instructions)) {
                $this->_instructions = $this->getMethod()->getInstructions();
            }
        }
        return $this->_instructions;
    }
}
