<?php

class IvendPayLibrary
{
    protected $secretKey = '';

    public function __construct($secretKey)
    {
        $this->secretKey = $secretKey;
    }

    private function isSetup()
    {
        return !empty($this->secretKey);
    }

    protected function curlOptions($url, $dataValue = [], $method = 'POST')
    {
        return [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => json_encode($dataValue),
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "X-API-KEY: " . $this->secretKey
            ]
        ];
    }

    public function createOrderCurl($dataValue) {
        if (!$this->isSetup()) {
            return array('error' => 'Account details must be configured before using this module.');
        }

        $curl = curl_init();

        curl_setopt_array($curl, $this->curlOptions(
            'https://invoice.ivendpay.com/api/order',
            $dataValue
        ));

        $response = json_decode(curl_exec($curl), true);

        if ($response['status'] !== 'success' ||
            empty($response['data']['url']) ||
            empty($response['data']['order_id']))
        {
            return array('error' => $response['message']);
        }

        return $response['data'];
    }

    public function getCallbackData()
    {
        return json_decode(html_entity_decode(file_get_contents('php://input')), true);
    }

    public function checkHeaderApiKey()
    {
        $checkHeaderApiKey = false;
        $headers = apache_request_headers();

        foreach ($headers as $header => $value) {
            if (mb_strtoupper($header) === 'X-API-KEY') {
                if ($value === $this->secretKey) {
                    $checkHeaderApiKey = true;
                    break;
                }
            }
        }

        return $checkHeaderApiKey;
    }

    public function get_remote_order_details($invoice) {
        $apiKey = $this->secretKey;

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://gate.ivendpay.com/api/v3/bill/" . $invoice,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "X-API-KEY: " . $apiKey
            ]
        ]);

        $response = json_decode(curl_exec($curl), true);

        if (empty($response['data'][0]['status'])) {
            return false;
        }

        return $response['data'][0];
    }
}
