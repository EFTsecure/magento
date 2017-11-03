<?php
namespace Eftsecure\Payment\Controller\Checkout;
use Magento\Braintree\Model\PaymentMethod;
use Magento\Framework\App\Action\Context;
use Magento\Sales\Model\Order;
class Success extends \Magento\Framework\App\Action\Action
{
	protected $_checkoutSession;
	protected $_orderFactory;
	protected $_storeManager;
	protected $_scopeConfig;
	protected $_encryptor;
 
    public function __construct(Context $context, \Magento\Checkout\Model\Session $checkoutSession, \Magento\Sales\Model\OrderFactory $orderFactory)
    {
		$this->_orderFactory = $orderFactory;
        parent::__construct($context);
    }
	
	public function execute()
	{
		if ($this->getRequest()->getPost("merchant_reference") && $this->getRequest()->getPost("callpay_transaction_id")) {
			$orderId = $this->getRequest()->getPost("merchant_reference");
			$order = $this->_orderFactory->create()->loadByIncrementId($orderId);
			if(!empty($order)) {
				$reason = $this->getRequest()->getPost("reason");
				$successful = $this->getRequest()->getPost("success");
				if ($successful == 1) {
					$state = Order::STATE_PAYMENT_REVIEW;
					$status = $state;
					$order->setState($state)->setStatus($status);
				} else {
					$order->addStatusHistoryComment($reason, false);
				}
				$order->save();
				echo json_encode(['reason'=>$reason,'successful'=>$successful]);
			} else {
				$this->_redirect('401');
			}
		   
		} else {
			$this->_redirect('401');
		}
	}
}
?>