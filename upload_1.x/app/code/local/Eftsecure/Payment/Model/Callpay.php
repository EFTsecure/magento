<?php
/**
 *
 */

/**
 * Class Eftsecure_Payment_Model_Callpay
 */
class Eftsecure_Payment_Model_Callpay {

    const SERVICES_URL = 'https://services.callpay.com/';

    const EFTSECURE_URL = 'https://eftsecure.callpay.com/';

    public function getToken($username, $password, $json = false) {

        $objCurl = new Varien_Http_Adapter_Curl();

        $objCurl->setConfig(array(
            'userpwd'    => $username . ":" . $password,
            'header'     => false,
            'verifypeer' => 0
        ));

        $objCurl->write(Zend_Http_Client::POST, self::EFTSECURE_URL . 'api/v1/token');

        $response = $objCurl->read();

        if ($objCurl->getErrno()) {

            $errNo  = $objCurl->getErrno();
            $errMsg = $objCurl->getError();
            $objCurl->close();

            throw new Eftsecure_Payment_Exception('(' . $errNo . ') ' . $errMsg, 1001);
        }

        $objCurl->close();

        /** @var Mage_Core_Helper_Data $coreHelper */
        $coreHelper = Mage::helper('core');

        try {

            $arrResponse = $coreHelper->jsonDecode($response);

            // Checking if error occured
            if (isset($arrResponse['code']) && $arrResponse['code'] == 0) {
                if (isset($arrResponse['name']) && $arrResponse['name'] == 'Unauthorized') {
                    throw new Eftsecure_Payment_Exception($arrResponse['message'], 1002);
                } elseif (isset($arrResponse['name']) && isset($arrResponse['message'])) {
                    $err = $arrResponse['name'] . ': ' . $arrResponse['message'];
                    throw new Eftsecure_Payment_Exception($err, 1000);
                } else {
                    throw new Eftsecure_Payment_Exception('Unkown Error Occured', 1000);
                }
            }

            if (isset($arrResponse['token']) && isset($arrResponse['expires'])) {

                if ($json) {
                    return $response;
                }

                return $arrResponse;

            } else {
                throw new Eftsecure_Payment_Exception('Unkown Error Occured', 1000);
            }

        } catch (Zend_Json_Exception $e) {
            throw new Eftsecure_Payment_Exception('Invalid JSON Response: ' . $e->getMessage(), 1003);
        }
    }

    public function retrieveTransaction($token, $transactionId)
    {
        $objCurl = new Varien_Http_Adapter_Curl();

        $objCurl->setConfig(array(
            'header'     => false,
            'verifypeer' => 0
        ));

        $objCurl->write(
            Zend_Http_Client::GET,
            self::EFTSECURE_URL . 'api/v1/gateway-transaction/' . $transactionId,
            '1.1',
            array(
                'X-Token:' . $token
            )
        );

        $response = $objCurl->read();

        if ($objCurl->getErrno()) {

            $errNo  = $objCurl->getErrno();
            $errMsg = $objCurl->getError();
            $objCurl->close();

            throw new Eftsecure_Payment_Exception('(' . $errNo . ') ' . $errMsg, 1001);
        }

        $objCurl->close();

        /** @var Mage_Core_Helper_Data $coreHelper */
        $coreHelper = Mage::helper('core');

        try {

            $arrResponse = $coreHelper->jsonDecode($response);

            // Checking if error occured
            if (isset($arrResponse['code']) && $arrResponse['code'] == 0) {
                if (isset($arrResponse['name']) && $arrResponse['name'] == 'Unauthorized') {
                    throw new Eftsecure_Payment_Exception($arrResponse['message'], 1002);
                } elseif (isset($arrResponse['name']) && isset($arrResponse['message'])) {
                    $err = $arrResponse['name'] . ': ' . $arrResponse['message'];
                    throw new Eftsecure_Payment_Exception($err, 1000);
                } else {
                    throw new Eftsecure_Payment_Exception('Unkown Error Occured', 1000);
                }
            }

            if (isset($arrResponse['id'])) {
                return $arrResponse;
            } else {
                throw new Eftsecure_Payment_Exception('Unkown Result', 1000);
            }

        } catch (Zend_Json_Exception $e) {
            throw new Eftsecure_Payment_Exception('Invalid JSON Response: ' . $e->getMessage(), 1003);
        }
    }

    public function refund($token, $transactionId) {

        $objCurl = new Varien_Http_Adapter_Curl();

        $objCurl->setConfig(array(
            'header'     => false,
            'verifypeer' => 0
        ));

        $objCurl->addOption(CURLOPT_CUSTOMREQUEST, 'PUT');

        $objCurl->write(
            Zend_Http_Client::PUT,
            self::EFTSECURE_URL . 'api/v1/gateway-transaction/refund/' . $transactionId,
            '1.1',
            array(
                'X-Token:' . $token
            )
        );

        $response = $objCurl->read();

        if ($objCurl->getErrno()) {

            $errNo  = $objCurl->getErrno();
            $errMsg = $objCurl->getError();
            $objCurl->close();

            throw new Eftsecure_Payment_Exception('(' . $errNo . ') ' . $errMsg, 1001);
        }

        $objCurl->close();

        /** @var Mage_Core_Helper_Data $coreHelper */
        $coreHelper = Mage::helper('core');

        try {

            $arrResponse = $coreHelper->jsonDecode($response);

            // Checking if error occured
            if (isset($arrResponse['code']) && $arrResponse['code'] == 0) {
                if (isset($arrResponse['name']) && $arrResponse['name'] == 'Unauthorized') {
                    throw new Eftsecure_Payment_Exception($arrResponse['message'], 1002);
                } elseif (isset($arrResponse['name']) && isset($arrResponse['message'])) {
                    $err = $arrResponse['name'] . ': ' . $arrResponse['message'];
                    throw new Eftsecure_Payment_Exception($err, 1000);
                } else {
                    throw new Eftsecure_Payment_Exception('Unkown Error Occured', 1000);
                }
            }

            if (isset($arrResponse['result'])) {
                return $arrResponse['result'];
            } else {
                throw new Eftsecure_Payment_Exception('Unkown Result', 1000);
            }

        } catch (Zend_Json_Exception $e) {
            throw new Eftsecure_Payment_Exception('Invalid JSON Response: ' . $e->getMessage(), 1003);
        }
    }

    public function getPaymentKey($username, $password, $reference, $amount) {

        $objCurl = new Varien_Http_Adapter_Curl();

        $objCurl->setConfig(array(
            'userpwd'    => $username . ":" . $password,
            'header'     => false,
            'verifypeer' => 0
        ));

        $objCurl->write(
            Zend_Http_Client::POST,
            self::EFTSECURE_URL . 'api/v1/eft/payment-key',
            '1.1',
            array(),
            array(
                'merchant_reference' => $reference,
                'amount'             => $amount
            )
        );

        $response = $objCurl->read();

        if ($objCurl->getErrno()) {

            $errNo  = $objCurl->getErrno();
            $errMsg = $objCurl->getError();
            $objCurl->close();

            throw new Eftsecure_Payment_Exception('(' . $errNo . ') ' . $errMsg, 1001);
        }

        $objCurl->close();

        /** @var Mage_Core_Helper_Data $coreHelper */
        $coreHelper = Mage::helper('core');

        try {

            $arrResponse = $coreHelper->jsonDecode($response);

            // Checking if error occured
            if (isset($arrResponse['code']) && $arrResponse['code'] == 0) {
                if (isset($arrResponse['name']) && $arrResponse['name'] == 'Unauthorized') {
                    throw new Eftsecure_Payment_Exception($arrResponse['message'], 1002);
                } elseif (isset($arrResponse['name']) && isset($arrResponse['message'])) {
                    $err = $arrResponse['name'] . ': ' . $arrResponse['message'];
                    throw new Eftsecure_Payment_Exception($err, 1000);
                } else {
                    throw new Eftsecure_Payment_Exception('Unkown Error Occured', 1000);
                }
            }

            if (isset($arrResponse['key'])) {
                return $arrResponse['key'];
            } else {
                throw new Eftsecure_Payment_Exception('Unkown Error Occured', 1000);
            }

        } catch (Zend_Json_Exception $e) {
            throw new Eftsecure_Payment_Exception('Invalid JSON Response: ' . $e->getMessage(), 1003);
        }
    }
}
