<?php

namespace Callpay\Eftsecure\Api;

interface OrderManagementInterface
{
    /**
     * Get order by quote ID
     * @param int $quoteId
     * @return int|null
     */
    public function getOrderByQuoteId(int $quoteId): ?int;
}
