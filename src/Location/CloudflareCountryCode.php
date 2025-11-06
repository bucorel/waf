<?php
/** 
 * Author - Pushpendra Singh Thakur <thakur@bucorel.com>
 * Copyright - Business Computing Research Laboratory <www.bucorel.com>
 */
namespace Bucorel\Waf\Location;

/**
 * Class to get country code from Cloudflare HTTP_CF_IPCOUNTRY header
 */
class CloudflareCountryCode{
	/**
	 * Get country code from Cloudflare HTTP_CF_IPCOUNTRY header
	 *
	 * @return string|false
	 */
	public static function get():string|false{
		if( isset( $_SERVER['HTTP_CF_IPCOUNTRY'] ) ){
			return $_SERVER['HTTP_CF_IPCOUNTRY'];
		}

		return false;
	}
}
?>
