<?php

namespace Callpay\Eftsecure\Model;

use GuzzleHttp\Exception\GuzzleException;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Callpay\Eftsecure\Api\SuccessApiInterface;
use Magento\Framework\Webapi\Exception as WebapiException;
use Magento\Framework\App\RequestInterface;
use Callpay\Eftsecure\Service\AuthService;
use GuzzleHttp\Client;

class SuccessApi implements SuccessApiInterface
{
    protected Session $_checkoutSession;
    protected OrderRepositoryInterface $_orderRepository;
    protected StoreManagerInterface $_storeManager;
    protected ScopeConfigInterface $_scopeConfig;
    protected LoggerInterface $logger;
    protected RequestInterface $request;
    protected AuthService $authService;
    protected Client $httpClient;
    /**
     * @param Session $checkoutSession
     * @param OrderRepositoryInterface $orderRepository
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param LoggerInterface $logger
     * @param RequestInterface $request
     * @param AuthService $authService
     * @param Client $httpClient
     */

    public function __construct(
        Session $checkoutSession,
        OrderRepositoryInterface $orderRepository,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        LoggerInterface $logger,
        RequestInterface $request,
        AuthService $authService,
        Client $httpClient
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_orderRepository = $orderRepository;
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;
        $this->logger = $logger;
        $this->request = $request;
        $this->authService = $authService;
        $this->httpClient = $httpClient;
    }

    /**
     * @return bool|string
     * @throws WebapiException
     */
    public function execute(): bool|string
    {
        // Retrieve POST data
        $rawData = $this->request->getContent();
        $postData =  json_decode($rawData, true);
        // Log the incoming data (optional)
        $this->logger->info('Webhook received: ' . json_encode($postData));
        if (isset($postData['merchant_reference']) && isset($postData['callpay_transaction_id'])) {
            $orderId = $postData["merchant_reference"];
            $transactionId = $postData["callpay_transaction_id"];
            $organisation_id = $this->_scopeConfig->getValue('payment/eftpay/eftsecure_organisation_id', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $salt = $this->_scopeConfig->getValue('payment/eftpay/eftsecure_salt', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $timestamp = time();
            $token = $this->authService->generateSha256($salt, $organisation_id, time());
            $baseUrl = $this->_scopeConfig->getValue('callpay/general/callpay_base_url', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

            try {
                // Set headers
                $headers = [
                    'Auth-Token' => $token,
                    'Org-Id' => $organisation_id,
                    'Timestamp' => $timestamp,
                    'Accept' => 'application/json'
                ];
                $url = $baseUrl . "/api/v2/gateway-transaction/{$transactionId}";
                // Send POST request
                $response = $this->httpClient->get($url, [
                    'headers' => $headers,
                    'verify' => false // Disable SSL verification
                ]);

                // Get response body
                $responseData = json_decode($response->getBody(), true);
                if ($responseData['amount'] == $postData["amount"] && $responseData['gateway_reference'] == $postData["gateway_reference"]) {
                    // Load order by increment ID
                    $order = $this->_orderRepository->get($orderId);
                    if ($order) {
                        $reason = $postData["reason"];
                        $successful = $postData["success"];
                        if ($successful == 1) {
                            $state = $this->_scopeConfig->getValue(
                                'payment/eftpay/success_status',
                                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                            );
                            if (empty($state)) {
                                $state = Order::STATE_PROCESSING;
                            }
                            $status = $state;

                        } else {
                            $state = $this->_scopeConfig->getValue(
                                'payment/eftpay/failure_status',
                                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                            );
                            $status = $state;
                            $order->addStatusHistoryComment($reason, false);
                        }

                        $order->setState($state)->setStatus($status);
                        try {
                            $orderCommentSender = ObjectManager::getInstance()->create('Magento\Sales\Model\Order\Email\Sender\OrderCommentSender');
                            $orderCommentSender->send($order, true, 'Payment successful');

                        } catch (\Exception $e) {
                            // Handle exception for order comment email sending
                        }
                        // Save order using repository
                        $this->_orderRepository->save($order);
                        return json_encode(['reason' => $reason, 'successful' => $successful]);
                    } else {
                        throw new WebapiException(__('Order not found'), 0, WebapiException::HTTP_UNAUTHORIZED);
                    }
                } else {
                    throw new WebapiException(__('Unauthorized'), 0, WebapiException::HTTP_UNAUTHORIZED);
                }
            } catch (\Exception $e) {
                // Handle order loading exception
                $this->logger->error('Error processing order: ' . $e->getMessage());
                throw new WebapiException(__('Unable to process order'), 0, WebapiException::HTTP_UNAUTHORIZED);
            } catch (GuzzleException $e) {
                $this->logger->error('Error during Guzzle request: ' . $e->getMessage());
            }
        } else {
            throw new WebapiException(__('Missing required parameters'), 0, WebapiException::HTTP_UNAUTHORIZED);
        }
    }
}
