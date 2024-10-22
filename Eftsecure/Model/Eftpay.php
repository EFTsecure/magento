<?php

namespace Callpay\Eftsecure\Model;

class Eftpay extends \Magento\Payment\Model\Method\AbstractMethod
{

    protected $_code = 'eftpay';

    protected $_isOffline = false;

    public function getInstructions()
    {
        return trim($this->getConfigData('instructions'));
    }
}
