<?php
// app/Services/MpesaService.php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class MpesaService
{
    private $config;

    public function __construct()
    {
        $this->config = [
            'base_url' => 'https://sandbox.safaricom.co.ke',
            'consumer_key' => 'XidOay4ivCgEyU0zITeYpxYJVWbNzvvC61YRKpGmvK5MgUSZ',
            'consumer_secret' => 'f8OqXGaF4WBZaCdwSmmHTlZ7NoPY2UjRFS7lVcPmlCu4OchTqYqplkxImz5ab94P',
            'passkey' => 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919',
            'shortcode' => '174379',
             'callback_url' => 'http://192.168.137.1:8000/api/mpesa/callback',

        ];
    }

    /**
     * Validate and format phone number for M-Pesa
     */
    public function validatePhoneNumber($phoneNumber)
    {
        // Remove any non-digit characters
        $cleanNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

        Log::info('Phone number validation', [
            'original' => $phoneNumber,
            'cleaned' => $cleanNumber,
            'length' => strlen($cleanNumber)
        ]);

        // Convert to 254 format
        if (strlen($cleanNumber) === 9 && in_array($cleanNumber[0], ['1', '7'])) {
            // Format: 712345678 -> 254712345678
            $formatted = '254' . $cleanNumber;
        } elseif (strlen($cleanNumber) === 10 && $cleanNumber[0] === '0') {
            // Format: 0712345678 -> 254712345678
            $formatted = '254' . substr($cleanNumber, 1);
        } elseif (strlen($cleanNumber) === 12 && substr($cleanNumber, 0, 3) === '254') {
            // Format: 254712345678 -> 254712345678
            $formatted = $cleanNumber;
        } else {
            Log::error('Invalid phone number format', ['number' => $phoneNumber]);
            return null;
        }

        // Validate the final format
        if (!preg_match('/^254[17]\d{8}$/', $formatted)) {
            Log::error('Invalid phone number after formatting', ['formatted' => $formatted]);
            return null;
        }

        Log::info('Phone number validated successfully', ['formatted' => $formatted]);
        return $formatted;
    }

    public function getAccessToken()
    {
        $cachedToken = Cache::get('mpesa_access_token');
        if ($cachedToken) {
            return $cachedToken;
        }

        try {
            $credentials = base64_encode($this->config['consumer_key'] . ':' . $this->config['consumer_secret']);

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $credentials,
            ])
            ->timeout(30)
            ->withoutVerifying()
            ->get($this->config['base_url'] . '/oauth/v1/generate?grant_type=client_credentials');

            if ($response->successful()) {
                $data = $response->json();

                if (!isset($data['access_token'])) {
                    Log::error('No access_token in response', ['response' => $data]);
                    return null;
                }

                $accessToken = trim($data['access_token']);
                Cache::put('mpesa_access_token', $accessToken, 55 * 60);

                Log::info('M-Pesa access token generated successfully');
                return $accessToken;
            }

            Log::error('Failed to get access token', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            return null;

        } catch (\Exception $e) {
            Log::error('Exception getting access token: ' . $e->getMessage());
            return null;
        }
    }

public function initiateSTKPush($phoneNumber, $amount, $accountReference, $transactionDesc)
{
    try {
        // Always get a fresh token to avoid caching issues
        Cache::forget('mpesa_access_token');
        $accessToken = $this->getAccessToken();

        if (!$accessToken) {
            return [
                'success' => false,
                'message' => 'Failed to get access token from M-Pesa'
            ];
        }

        // Validate and format phone number
        $formattedPhone = $this->validatePhoneNumber($phoneNumber);
        if (!$formattedPhone) {
            return [
                'success' => false,
                'message' => 'Invalid phone number format. Please use format: 0712345678 or 712345678'
            ];
        }

        // Ensure amount is integer for M-Pesa
        $amount = intval($amount);
        if ($amount <= 0) {
            return [
                'success' => false,
                'message' => 'Invalid amount. Amount must be greater than 0.'
            ];
        }

        Log::info('Attempting STK Push', [
            'original_phone' => $phoneNumber,
            'formatted_phone' => $formattedPhone,
            'amount' => $amount,
            'reference' => $accountReference
        ]);

        $timestamp = date('YmdHis');
        $password = base64_encode($this->config['shortcode'] . $this->config['passkey'] . $timestamp);

        $payload = [
            'BusinessShortCode' => $this->config['shortcode'],
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $amount, // Integer amount
            'PartyA' => $formattedPhone,
            'PartyB' => $this->config['shortcode'],
            'PhoneNumber' => $formattedPhone,
            'CallBackURL' => $this->config['callback_url'],
            'AccountReference' => $accountReference,
            'TransactionDesc' => $transactionDesc,
        ];

        // Rest of your existing code remains the same...
        Log::info('STK Push payload', $payload);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $accessToken,
        ])
        ->timeout(30)
        ->withoutVerifying()
        ->post($this->config['base_url'] . '/mpesa/stkpush/v1/processrequest', $payload);

        $responseData = $response->json();

        Log::info('STK Push response', [
            'status_code' => $response->status(),
            'response' => $responseData
        ]);

        if ($response->successful()) {
            if (isset($responseData['ResponseCode'])) {
                if ($responseData['ResponseCode'] == '0') {
                    return [
                        'success' => true,
                        'merchant_request_id' => $responseData['MerchantRequestID'],
                        'checkout_request_id' => $responseData['CheckoutRequestID'],
                        'response_description' => $responseData['ResponseDescription'],
                        'customer_message' => $responseData['CustomerMessage'] ?? 'Payment request sent to your phone',
                    ];
                } else {
                    $errorMessage = $this->getErrorDescription($responseData['ResponseCode']);
                    return [
                        'success' => false,
                        'message' => $errorMessage,
                        'error_code' => $responseData['ResponseCode']
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'message' => 'Invalid response from M-Pesa: No ResponseCode'
                ];
            }
        }

        // Handle specific phone number errors
        if (strpos($response->body(), 'Invalid PhoneNumber') !== false) {
            return [
                'success' => false,
                'message' => 'Invalid phone number. Please ensure you entered a valid Safaricom number (e.g., 0712345678)'
            ];
        }

        return [
            'success' => false,
            'message' => 'HTTP Error: ' . $response->status() . ' - ' . $response->body()
        ];

    } catch (\Exception $e) {
        Log::error('STK Push exception: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Service error: ' . $e->getMessage()
        ];
    }
}


// Add specific amount validation in the error method
private function getErrorDescription($errorCode)
{
    $errors = [
        '0' => 'Success',
        '1' => 'Insufficient funds',
        '2' => 'Amount is less than minimum transaction value',
        '3' => 'Amount is more than maximum transaction value',
        '4' => 'Would exceed daily transfer limit',
        '5' => 'Would exceed minimum balance',
        '6' => 'Unresolved primary party',
        '7' => 'Unresolved receiver party',
        '8' => 'Would exceed maximum balance',
        '11' => 'Debit account invalid',
        '12' => 'Credit account invalid',
        '13' => 'Unresolved debit account',
        '14' => 'Unresolved credit account',
        '15' => 'Duplicate detected',
        '17' => 'Internal failure',
        '20' => 'Unresolved initiator',
        '26' => 'Traffic blocking condition in place',
        '1032' => 'Request cancelled by user',
        '1037' => 'Timeout, unable to process request',
        '2001' => 'Invalid phone number or format',
    ];

    return $errors[$errorCode] ?? 'Unknown error: ' . $errorCode;
}

    /**
     * Test phone number validation
     */
    public function testPhoneNumber($phoneNumber)
    {
        $formatted = $this->validatePhoneNumber($phoneNumber);

        return [
            'original' => $phoneNumber,
            'formatted' => $formatted,
            'is_valid' => !is_null($formatted),
            'pattern' => $formatted ? 'Valid M-Pesa format' : 'Invalid format'
        ];
    }
}
