<?php

namespace Callpay\Eftsecure\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class RedirectConfigProvider implements ConfigProviderInterface
{
    const XML_PATH_REDIRECT_ENABLED = 'payment/eftpay/redirect';

    protected ScopeConfigInterface $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function getConfig(): array
    {
        return [
            'payment' => [
                'eftpay' => [
                    'redirect' => $this->scopeConfig->getValue(self::XML_PATH_REDIRECT_ENABLED, \Magento\Store\Model\ScopeInterface::SCOPE_STORE),
                ]
            ]
        ];
    }
}
