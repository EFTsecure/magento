<?php
namespace Callpay\Eftsecure\Block;

class Eft extends \Magento\Framework\View\Element\Template
{
	protected $_checkoutSession;
	protected $_orderFactory;
	protected $_scopeConfig;
	protected $_encryptor;

    public function __construct( \Magento\Framework\View\Element\Template\Context $context, \Magento\Checkout\Model\Session $checkoutSession, \Magento\Sales\Model\OrderFactory $orderFactory, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,  \Magento\Framework\Encryption\EncryptorInterface $encryptor)
    {
		$this->_checkoutSession = $checkoutSession;
		$this->_orderFactory = $orderFactory;
		$this->_scopeConfig = $scopeConfig;
		$this->_encryptor = $encryptor;
		parent::__construct($context);
    }

}
