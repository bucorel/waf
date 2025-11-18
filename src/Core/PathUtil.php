<?php
namespace Bucorel\Waf\Core;

class PathUtil{
	
	public static function getAssetPath( string $systemPath ) : string {
		return $systemPath.'assets/';
	}
	
	/* Templates */
	public static function getTemplatePath( string $systemPath ) : string {
		return self::getAssetPath( $systemPath ).'/templates/';
	}
	
	public static function getUiPath( string $systemPath ) : string {
		return self::getTemplatePath( $systemPath ).'ui/';
	}
	
	public static function getPagePath( string $systemPath ) : string {
		return self::getTemplatePath( $systemPath ).'pages/';
	}
	
	public static function getThemePath( string $systemPath ) : string {
		return self::getTemplatePath( $systemPath ).'themes/';
	}
	
	
	/* Dynamic contents */
	public static function getRegionalConfigPath( string $systemPath ) : string {
		return self::getAssetPath( $systemPath ).'dynamic/regions/';
	}
	
	public static function getLangpackPath( string $systemPath ) : string {
		return self::getAssetPath( $systemPath ).'dynamic/langpacks/';
	}
	
	public static function getGeoIpDbPath( string $systemPath ) : string {
		return self::getAssetPath( $systemPath ).'dynamic/geoipdb/';
	}
}
?>
