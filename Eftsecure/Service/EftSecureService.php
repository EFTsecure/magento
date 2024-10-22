<?php

namespace Callpay\Eftsecure\Service;

use GuzzleHttp\Client;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Sales\Model\OrderFactory;

class EftSecureService
{
    protected $httpClient;
    protected $storeManager;
    protected $scopeConfig;
    protected $encryptor;
    protected $authService;
    protected $orderFactory;

    public function __construct(
        Client $httpClient,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        EncryptorInterface $encryptor,
        AuthService $authService,
        OrderFactory $orderFactory
    ) {
        $this->httpClient = $httpClient;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->encryptor = $encryptor;
        $this->authService = $authService;
        $this->orderFactory = $orderFactory;
    }

    public function eftSecure($lastOrderId)
    {
        $order = $this->orderFactory->create()->loadByIncrementId($lastOrderId);
        $baseUrl = $this->storeManager->getStore()->getBaseUrl();
        // Check if the URL contains the word "callpay"
        if (str_contains($baseUrl, 'callpay')) {
            $baseUrl = str_replace('https://', 'http://', $baseUrl);
        }
        $organisationId = $this->scopeConfig->getValue('payment/eftpay/eftsecure_organisation_id', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $salt = $this->scopeConfig->getValue('payment/eftpay/eftsecure_salt', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $timestamp = time();
        $token = $this->authService->generateSha256($salt, $organisationId, $timestamp);
        $callpayBaseUrl = $this->scopeConfig->getValue('callpay/general/callpay_base_url', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        if ($token) {
            try {
                $successUrl = $baseUrl . 'checkout/onepage/success';
                $errorUrl = $baseUrl . 'checkout/onepage/failure';
                $magentoUrl = $baseUrl . 'rest/V1/eft/checkout/success';
                $notifyUrl = $callpayBaseUrl . '/connector/magento/notify?url=' . urlencode($magentoUrl);

                $params = [
                    "merchant_reference" => $lastOrderId,
                    "amount"             => $order->getGrandTotal()>0 ? number_format($order->getGrandTotal(), 2) : 0,
                    "notify_url"         => $notifyUrl,
                    "success_url"        => $successUrl,
                    "error_url"          => $errorUrl,
                    "cancel_url"         => $errorUrl,
                    "pending_url"        => $errorUrl
                ];

                $headers = [
                    'Auth-Token' => $token,
                    'Org-Id' => $organisationId,
                    'Timestamp' => $timestamp,
                    'Accept' => 'application/json'
                ];

                $response = $this->httpClient->post($callpayBaseUrl . '/api/v2/eft/payment-key', [
                    'headers' => $headers,
                    'form_params' => $params,
                    'verify' => false
                ]);
                return json_decode($response->getBody(), true);
            } catch (\Exception $e) {
                // Handle exceptions appropriately
                throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
            }
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(__('Unable to generate token.'));
        }
    }
}
