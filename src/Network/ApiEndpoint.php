<?php
	/**
	 * @copyright Business Computing Research Laboratory <www.bucorel.com>
	 * @author Pushpendra Singh Thakur <thakurpsr@gmail.com>
	 */
	
	namespace Bucorel\Waf\Network;
	
	class ApiEndpoint extends RequestHandler{
		function init(){
			$this->basicAuthCheck();
		}
		
		function basicAuthCheck(){
			if( defined( 'AUTH_LIST' ) && is_array( AUTH_LIST ) ){
				if( !isset( $_SERVER['PHP_AUTH_USER'] ) || !isset( $_SERVER['PHP_AUTH_PW'] )){
					$this->showError( self::STATUS_UNAUTHORIZED, 'UNAUTHORIZED' );
				}
				
				if( !isset( AUTH_LIST[ $_SERVER['PHP_AUTH_USER'] ] ) || AUTH_LIST[ $_SERVER['PHP_AUTH_USER'] ] != $_SERVER['PHP_AUTH_PW'] ){
					$this->showError( self::STATUS_UNAUTHORIZED, 'UNAUTHORIZED' );
				}
			}
		}
	}
?>
