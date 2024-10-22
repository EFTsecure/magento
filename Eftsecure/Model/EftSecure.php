<?php

namespace Callpay\Eftsecure\Model;

use Callpay\Eftsecure\Api\EftSecureInterface;
use Callpay\Eftsecure\Service\EftSecureService;

class EftSecure implements EftSecureInterface
{
    protected EftSecureService $eftSecureService;

    public function __construct(EftSecureService $eftSecureService)
    {
        $this->eftSecureService = $eftSecureService;
    }

    /**
     * @param int $orderId
     * @return bool|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function eftSecure(int $orderId): bool|string
    {
        return json_encode($this->eftSecureService->eftSecure($orderId));
    }
}

