<?php

namespace Callpay\Eftsecure\Model;

use Callpay\Eftsecure\Api\OrderManagementInterface;
use Magento\Sales\Model\OrderFactory;
class OrderManagement implements OrderManagementInterface
{
    protected OrderFactory $orderFactory;

    public function __construct(OrderFactory $orderFactory)
    {
        $this->orderFactory = $orderFactory;
    }

    /**
     * Get order by quote ID
     * @param int $quoteId
     * @return int|null
     */
    public function getOrderByQuoteId(int $quoteId): ?int
    {
        // Load the order by quote ID
        $order = $this->orderFactory->create()->loadByAttribute('quote_id', $quoteId);

        if ($order && $order->getId()) {
            return $order->getId(); // Return the order ID if found
        }

        return null; // No order found
    }
}
