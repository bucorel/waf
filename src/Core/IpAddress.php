<?php
/** 
 * Author - Pushpendra Singh Thakur <thakurpsr@gmail.com>
 * Copyright - Business Computing Research Laboratory <www.bucorel.com>
 */
namespace Bucorel\Waf\Core;

class IpAddress{
	/**
	 * Validate if the provided string is a valid IP address (IPv4 or IPv6)
	 *
	 * @param string $ip
	 * @return bool
	 */
	public static function isValid( string $ip ):bool{
		return filter_var($ip, FILTER_VALIDATE_IP) !== false;
	}

	public static function isPrivate( string $ip ):bool{
		return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false;
	}

	public static function get():string|false{
		$ip = null;

		// 1. Cloudflare
		if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
			$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
		}
		// 2. Other proxy headers
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			// Can be comma-separated list
			$ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
			$ip = trim($ips[0]);
		}
		elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}
		// 3. Remote address
		elseif (!empty($_SERVER['REMOTE_ADDR'])) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		// Validate and filter private/reserved IPs
		if ($ip && self::isValid($ip) && !self::isPrivate($ip)) {
			return $ip;
		}

		return false; // No valid public IP found
	}
}
?>
