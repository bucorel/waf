<?php
/**
 * @Author - Pushpendra Singh Thakur <thakurpsr@gmail.com>
 * @Copyright - Business Computing Research Laboratory <www.bucorel.com>
 */
namespace Bucorel\Waf\Location;

/**
 * Class to get country code from ip-api.com service using IP address
 */
class IpApiCountryCode{
	/**
	 * Get country code from ip-api.com service using IP address
	 *
	 * @param string $ip
	 * @return string|false
	 */
	public static function get( string $ip ):string|false{
		$response = @file_get_contents("http://ip-api.com/json/{$ip}?fields=countryCode");
		if($response !== false){
			$data = json_decode($response, true);
			if(isset($data['countryCode'])){
				return $data['countryCode'];
			}
		}
		return false;
	}
}
?>