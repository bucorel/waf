<?php
/**
 * @Author - Pushpendra Singh Thakur <thakurpsr@gmail.com>
 * @Copyright - Business Computing Research Laboratory <www.bucorel.com>
 */
namespace Bucorel\Waf\Location;

/**
 * Class to get country code from ip-api.com service using IP address
 */
class IpApiClient{
    /**
     * Get country code from ip-api.com service using IP address
     *
     * @param string $ip
     * @return string
     * @throws \Exception
     */
    public static function get( string $ip ):array{    	
        $ch = curl_init();
        $url = "http://ip-api.com/json/{$ip}?fields=countryCode,status";

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5); // 5-second timeout
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3); // 3-second connection timeout

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            throw new \Exception("cURL Error: " . $error_msg);
        }

        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code !== 200) {
            throw new \Exception("HTTP Error: " . $http_code);
        }
        
        $data = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Invalid JSON response: " . json_last_error_msg());
        }
        
        if (isset($data['status']) && $data['status'] === 'fail') {
             throw new \Exception("ip-api.com service failed for IP: " . $ip);
        }

        if (!isset($data['countryCode'])) {
            throw new \Exception("Missing 'countryCode' in response from ip-api.com");
        }
        
        return $data;
    }
}
