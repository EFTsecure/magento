<?php

namespace Callpay\Eftsecure\Controller\Transaction;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Sales\Api\OrderRepositoryInterface;

class Status extends Action
{
    protected $resultJsonFactory;
    protected $transactionRepository;
    private OrderRepositoryInterface $orderRepository;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->orderRepository = $orderRepository;
        parent::__construct($context);
    }

    public function execute()
    {
        $transactionId = $this->getRequest()->getParam('transactionId');

        // Retrieve the status from the repository
        $order = $this->orderRepository->get($transactionId);
        $status = $order->getStatus();

        $result = $this->resultJsonFactory->create();
        return $result->setData(['status' => $status]);
    }
}

