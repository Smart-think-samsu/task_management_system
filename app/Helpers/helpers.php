<?php

use Illuminate\Support\Facades\Http;

if (!function_exists('sendSms')) {
    function sendSms($sms_body, $phone_number)
    {

        $url = 'https://ekdak.com/message-broker/send-sms/';
        $headers = [
            'Authorization' => 'Token 8d3690ef76134d9abd78f9cbde655dd46446a032'
        ];
        $form_data = [
            'sms_body' => $sms_body,
            'phone_number' => $phone_number
        ];

        $response = Http::withHeaders($headers)->post($url, $form_data);

        return $response;
    }
}

if (!function_exists('asset_image_path')) {
    /**
     * Get the URL to the asset image path.
     *
     * @param  string  $path
     * @return string
     */
    function asset_image_path($path)
    {
        return asset('assets/dist/img/site_image/' . $path);
    }
}
