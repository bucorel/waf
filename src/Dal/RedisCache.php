<?php
/** 
 * Author - Pushpendra Singh Thakur <thakur@bucorel.com>
 * Copyright - Business Computing Research Laboratory <www.bucorel.com>
 */

namespace Bucorel\Waf\Dal;

class RedisCache{
	
	protected $redis = null;
	protected $host = 'localhost';
	protected $port = 6379;
	
	function __construct( $host='localhost', $port=6379 ){
		$this->host = $host;
		$this->port = $port;
	}
	
	function connect(){
		if( !$this->redis ){
			$this->redis = new \Redis();
			$this->redis->connect( $this->host, $this->port );
		}
	}
	
	function set( $key, $value, $expiry=0 ){
		$this->connect();
		if( $expiry > 0 ){
			$this->redis->setex( $key, $expiry, $value );
		}else{
			$this->redis->set( $key, $value );
		}
	}
	
	function get( $key ){
		$this->connect();
		return $this->redis->get( $key );
	}
}
?>
