<?php

namespace App\Http\Controllers\Api;

use GuzzleHttp\Client;
// use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Psr7\Request;

class IpaymuController extends Controller
{
    function listPayment()
    {
        $client = new Client();
        $httpMethod = "GET";
        $vaNumber = config('services.ipaymu.va');
        $body = "";
        $requestBody = json_encode($body, JSON_UNESCAPED_SLASHES);
        $signature = $this->generateSignature($httpMethod, $vaNumber, $requestBody);
        $headers = [
            'Content-Type' => 'application/json',
            'signature' => $signature,
            'va' => $vaNumber,
            'timestamp' => date('YmdHis')
        ];
        $response = $client->request($httpMethod, 'https://sandbox.ipaymu.com/api/v2/payment-channels', [
            'headers' => $headers
        ]);
        $res = $response->getBody();
        dd($res->getContents());
    }
    function listTransaction()
    {
        $client = new Client();
        $httpMethod = "POST";
        $vaNumber = config('services.ipaymu.va');
        $timestamp = date('YmdHis');
        $body = [
            'transactionId' => '37911',
            'account' => '1179005290724894'
        ];

        $requestBody = json_encode($body, JSON_UNESCAPED_SLASHES);
        $signature = $this->generateSignature($httpMethod, $vaNumber, $requestBody);
        $headers = [
            'Content-Type' => 'application/json',
            'va' => $vaNumber,
            'timestamp' => $timestamp,
            'signature' => $signature,
        ];
        $response = $client->request($httpMethod, 'https://sandbox.ipaymu.com/api/v2/transaction', [
            'headers' => $headers,
            'body' => $requestBody
        ]);
        $res = $response->getBody();
        dd($res->getContents());
    }
    function generateSignature($httpMethod, $vaNumber, $requestBody)
    {
        $apiKey = config('services.ipaymu.apikey');
        $vaNumber = config('services.ipaymu.va');
        $hashedBody = strtolower(hash('sha256', $requestBody));
        $stringToSign = "{$httpMethod}:{$vaNumber}:{$hashedBody}:{$apiKey}";
        $signature = hash_hmac('sha256', $stringToSign, $apiKey);
        return $signature;
    }
}
