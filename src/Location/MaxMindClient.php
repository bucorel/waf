<?php
namespace Bucorel\Waf\Location;
use GeoIp2\Database\Reader;

class MaxMindClient{
	
	public static function getCountryCode( string $dbPath, string $ipAddress ):string{
		$reader = new Reader( $dbPath );

		// Query the database for the IP address
		$record = $reader->country( $ipAddress );

		// Get the country code (e.g., 'US')
		return $record->country->isoCode;
	}
}
?>
