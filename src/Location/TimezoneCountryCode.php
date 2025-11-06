<?php
/** 
 * Author - Pushpendra Singh Thakur <thakur@bucorel.com>
 * Copyright - Business Computing Research Laboratory <www.bucorel.com>
 * 
 * if the site is behind cloudflare proxy HTTP_CF_IPCOUNTRY header
 * contains country code like IN,US etc
 */
namespace Bucorel\Waf\Location;

/**
 * Class to get country code from timezone ID provided in the request
 */
class TimezoneCountryCode{
	/**
	 * Get country code from timezone ID
	 * @param string $timezone
	 * @return string|false
	 */
	public static function get( string $timezone ):string|false{
		try {
			$tz = new \DateTimeZone( $timezone );
			$location = $tz->getLocation();
			if ( isset( $location['country_code'] ) ) {
				return $location['country_code'];
			}
		} catch ( \Exception $e ) {
			//mostly if timezone id is invalid like Asia/Calcutta
			// Log the error: echo "Timezone error: " . $e->getMessage();
			return false;
		}

		return false;
	}
}
?>
