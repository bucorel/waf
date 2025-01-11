<?php
	namespace Bucorel\Waf\Location;

	class IpLocation{

		const SERVER_URL = 'http://ip-api.com/json/';
		const CACHE_EXPIRY = 43200;	//12 hours

		protected $cache = null;

		function __construct( $redisObj = null ){
			$this->cache = $redisObj;
		}

		public static function isPublicIp( $ip ){
			return filter_var(
				$ip, 
				FILTER_VALIDATE_IP, 
				FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE |  FILTER_FLAG_NO_RES_RANGE
			);
		}
		
		public static function getIp(){
			if( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '' ){
				return $_SERVER['HTTP_X_FORWARDED_FOR'];
			}else{
				return $_SERVER['REMOTE_ADDR'];
			}
		}

		function fetch(){
			$ip = self::getIp();
			
			if( self::isPublicIp( $ip ) == false ){
				$ip = '';
			}
			
			if( $data = $this->fetchFromCache( $ip ) ){
				return $data;
			}

			$data = $this->fetchFromIpApi( $ip );
			$this->cacheIt( $ip, $data );
			return $data;
		}

		function fetchFromCache( $ip ){
			$key = $this->getCacheKey( $ip );
			if( $this->cache ){
				return $this->cache->get( $key );
			}
		}

		function cacheIt( $ip, $data ){
			$key = $this->getCacheKey( $ip );
			if( $this->cache ){
				return $this->cache->set( $key, $data, self::CACHE_EXPIRY );
			}
		}

		function getCacheKey( $ip ){
			return sha1( 'IP_'.$ip );
		}

		function fetchFromIpApi( $ip ){
			$url = self::SERVER_URL.$ip;
			$ch = curl_init();

			// set URL and other appropriate options
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15); 
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);

			// grab URL and pass it to the browser
			$json = curl_exec($ch);

			// close cURL resource, and free up system resources
			curl_close($ch);
			
			//echo $json;
			
			if( $data = json_decode( $json,true )){
				if( $data['status'] == 'success' ){
					return $data;
				}else{
					throw new \Exception( 'LOCATION_QUERY_FAILED' );
				}
			}else{
				throw new \Exception( 'BAD_LOCATION_DATA' );
			}
		}
	}
?>