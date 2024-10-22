<?php

namespace Callpay\Eftsecure\Api;

interface EftSecureInterface
{
    /**
     * @param int $orderId
     * @return bool|string
     */
    public function eftSecure(int $orderId) : bool|string;
}
