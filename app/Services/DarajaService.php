<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class DarajaService
{
    private $baseUrl;
    private $consumerKey;
    private $consumerSecret;
    private $passkey;
    private $shortcode;
    private $callbackUrl;

     public function __construct()
    {
        $this->baseUrl = config('services.mpesa.base_url');
        $this->consumerKey = config('services.mpesa.consumer_key');
        $this->consumerSecret = config('services.mpesa.consumer_secret');
        $this->passkey = config('services.mpesa.passkey');
        $this->shortcode = config('services.mpesa.shortcode');
        $this->callbackUrl = config('services.mpesa.callback_url');

        // Validate that all required credentials are set
        $this->validateCredentials();
    }

    /**
     * Validate that all required credentials are present
     */
    private function validateCredentials()
    {
        $required = [
            'base_url' => $this->baseUrl,
            'consumer_key' => $this->consumerKey,
            'consumer_secret' => $this->consumerSecret,
            'passkey' => $this->passkey,
            'shortcode' => $this->shortcode,
            'callback_url' => $this->callbackUrl,
        ];

        $missing = [];
        foreach ($required as $key => $value) {
            if (empty($value)) {
                $missing[] = $key;
            }
        }

        if (!empty($missing)) {
            throw new \Exception(
                'Missing M-Pesa configuration: ' . implode(', ', $missing) .
                '. Please check your .env file and config/services.php'
            );
        }
    }
    /**
     * Get access token for Daraja API
     */
    public function getAccessToken()
    {
        // Check if we have a cached token
        $cachedToken = Cache::get('mpesa_access_token');
        if ($cachedToken) {
            return $cachedToken;
        }

        try {
            $response = Http::withBasicAuth($this->consumerKey, $this->consumerSecret)
                ->timeout(30)
                ->retry(3, 100)
                ->get($this->baseUrl . '/oauth/v1/generate?grant_type=client_credentials');

            if ($response->successful()) {
                $data = $response->json();
                $accessToken = $data['access_token'];

                // Cache the token for 55 minutes (tokens expire in 1 hour)
                Cache::put('mpesa_access_token', $accessToken, 55 * 60);

                return $accessToken;
            }

            Log::error('Failed to get access token', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('Error getting access token', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Initiate STK Push payment
     */
    public function initiateSTKPush($phoneNumber, $amount, $accountReference, $transactionDesc)
    {
        try {
            $accessToken = $this->getAccessToken();

            if (!$accessToken) {
                return [
                    'success' => false,
                    'message' => 'Failed to authenticate with payment service'
                ];
            }

            $timestamp = date('YmdHis');
            $password = base64_encode($this->shortcode . $this->passkey . $timestamp);

            $payload = [
                'BusinessShortCode' => $this->shortcode,
                'Password' => $password,
                'Timestamp' => $timestamp,
                'TransactionType' => 'CustomerPayBillOnline',
                'Amount' => $amount,
                'PartyA' => $phoneNumber,
                'PartyB' => $this->shortcode,
                'PhoneNumber' => $phoneNumber,
                'CallBackURL' => $this->callbackUrl,
                'AccountReference' => $accountReference,
                'TransactionDesc' => $transactionDesc,
            ];

            Log::info('Initiating STK Push', [
                'phone' => $phoneNumber,
                'amount' => $amount,
                'reference' => $accountReference
            ]);

            $response = Http::withToken($accessToken)
                ->timeout(30)
                ->retry(2, 100)
                ->post($this->baseUrl . '/mpesa/stkpush/v1/processrequest', $payload);

            $responseData = $response->json();

            Log::info('STK Push Response', $responseData);

            if ($response->successful() && isset($responseData['ResponseCode']) && $responseData['ResponseCode'] == '0') {
                return [
                    'success' => true,
                    'merchant_request_id' => $responseData['MerchantRequestID'],
                    'checkout_request_id' => $responseData['CheckoutRequestID'],
                    'response_description' => $responseData['ResponseDescription'],
                    'customer_message' => $responseData['CustomerMessage'] ?? 'Payment request sent',
                ];
            }

            $errorMessage = $responseData['errorMessage'] ??
                           $responseData['ResponseDescription'] ??
                           'Payment request failed';

            Log::error('STK Push failed', [
                'response' => $responseData,
                'status' => $response->status()
            ]);

            return [
                'success' => false,
                'message' => $errorMessage,
                'error_code' => $responseData['ResponseCode'] ?? 'UNKNOWN'
            ];

        } catch (\Exception $e) {
            Log::error('Error initiating STK Push', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'success' => false,
                'message' => 'Payment service temporarily unavailable. Please try again.'
            ];
        }
    }

    /**
     * Check transaction status
     */
    public function checkTransactionStatus($checkoutRequestId)
    {
        try {
            $accessToken = $this->getAccessToken();

            if (!$accessToken) {
                return [
                    'success' => false,
                    'message' => 'Failed to authenticate with payment service'
                ];
            }

            $timestamp = date('YmdHis');
            $password = base64_encode($this->shortcode . $this->passkey . $timestamp);

            $payload = [
                'BusinessShortCode' => $this->shortcode,
                'Password' => $password,
                'Timestamp' => $timestamp,
                'CheckoutRequestID' => $checkoutRequestId,
            ];

            $response = Http::withToken($accessToken)
                ->timeout(30)
                ->post($this->baseUrl . '/mpesa/stkpushquery/v1/query', $payload);

            $responseData = $response->json();

            Log::info('Transaction Status Check', [
                'checkout_request_id' => $checkoutRequestId,
                'response' => $responseData
            ]);

            if ($response->successful() && isset($responseData['ResultCode'])) {
                return [
                    'success' => true,
                    'result_code' => $responseData['ResultCode'],
                    'result_description' => $responseData['ResultDesc'],
                    'response_data' => $responseData,
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to check transaction status',
                'response' => $responseData
            ];

        } catch (\Exception $e) {
            Log::error('Error checking transaction status', [
                'error' => $e->getMessage(),
                'checkout_request_id' => $checkoutRequestId
            ]);
            return [
                'success' => false,
                'message' => 'Service temporarily unavailable'
            ];
        }
    }

    /**
     * Validate phone number format
     */
    public function validatePhoneNumber($phoneNumber)
    {
        // Remove any non-digit characters
        $cleanNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

        // Convert to 254 format if needed
        if (strlen($cleanNumber) === 9 && $cleanNumber[0] === '7') {
            return '254' . $cleanNumber;
        } elseif (strlen($cleanNumber) === 10 && $cleanNumber[0] === '0') {
            return '254' . substr($cleanNumber, 1);
        } elseif (strlen($cleanNumber) === 12 && substr($cleanNumber, 0, 3) === '254') {
            return $cleanNumber;
        }

        return null;
    }

    /**
     * Get transaction status description
     */
    public function getStatusDescription($resultCode)
    {
        $statusMap = [
            '0' => 'Payment completed successfully',
            '1' => 'Insufficient funds',
            '1032' => 'Request cancelled by user',
            '1037' => 'Timeout, unable to process request',
            '2001' => 'Invalid phone number or format',
            '17' => 'Transaction failed',
            '20' => 'Invalid transaction',
        ];

        return $statusMap[$resultCode] ?? 'Transaction failed with code: ' . $resultCode;
    }
}
