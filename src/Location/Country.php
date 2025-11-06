<?php
/** 
 * Author - Pushpendra Singh Thakur <thakurpsr@gmail.com>
 * Copyright - Business Computing Research Laboratory <www.bucorel.com>
 */
namespace Bucorel\Waf\Location;
use Bucorel\Waf\Core\IpAddress;

/**
 * Class to determine the country code of the client using multiple methods
 */
class Country{
	/**
	 * Get the current country code using multiple methods
	 *
	 * @return string|false
	 */
	public static function getCurrentCountryCode( string $appMode, string $ipDbPath ):string|false{
		// 0. If App Mode is debug no need to determine
		if( $appMode == 'debug' ){
			return 'IN';
		}

		// 1. Try Cloudflare header first
		$countryCode = CloudflareCountryCode::get();
		if($countryCode !== false){
			return $countryCode;
		}

		//1.2 Try MaxMind
		$ip = IpAddress::get();

		if( $ip ){
			$countryCode = MaxMindClient::getCountryCode( $ipDbPath, $ip );
			if($countryCode !== false){
				return $countryCode;
			}
		}
		
		// 2. Try timezone based country code
		if( !empty( $_REQUEST['_ctz'] ) ){
			$countryCode = TimezoneCountryCode::get( $_REQUEST['_ctz'] );
			if($countryCode !== false){
				return $countryCode;
			}
		}

		if(!$ip){
			$ip = '';
		}
		
		// 3. Fallback to ip-api.com service
		$data = IpApiClient::get( $ip );
		if( isset( $data['countryCode'] ) ){
			return $data['countryCode'];
		}
		
		return false; // No valid country code found
	}
}
?>
