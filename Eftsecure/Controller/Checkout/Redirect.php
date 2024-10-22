<?php
namespace Callpay\Eftsecure\Controller\Checkout;

use Magento\Framework\App\Action\Context;
use Callpay\Eftsecure\Service\EftSecureService;
use \Magento\Store\Model\StoreManagerInterface;
use \Magento\Checkout\Model\Session;
class Redirect extends \Magento\Framework\App\Action\Action
{
	protected Session $_checkoutSession;
	protected StoreManagerInterface $_storeManager;
    protected EftSecureService $_eftSecureService;

    public function __construct(
        Context $context,
        Session $checkoutSession,
        StoreManagerInterface $storeManager,
        EftSecureService $eftSecureService
    )
    {
        $this->_eftSecureService = $eftSecureService;
		$this->_checkoutSession = $checkoutSession;
        $this->_storeManager = $storeManager;

        parent::__construct($context);
    }
    public function execute()
    {
        $last_order_id = $this->_checkoutSession->getLastRealOrder()->getIncrementId();
        if($last_order_id){
            try {
                $responseData = $this->_eftSecureService->eftSecure($last_order_id);
                $url = $responseData['url'];
                $splitUrl = explode('&', $url);
                $this->_redirect($splitUrl[0]);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->_redirect($this->_storeManager->getStore()->getBaseUrl());
            }
        } else {
            $this->_redirect($this->_storeManager->getStore()->getBaseUrl());
        }
    }
}
