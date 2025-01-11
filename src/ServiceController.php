<?php
	/**
	 * @copyright Business Computing Research Laboratory <www.bucorel.com>
	 * @author Pushpendra Singh Thakur <thakurpsr@gmail.com>
	 */

	namespace Bucorel\Waf;
	
	class ServiceController extends RequestHandler{

		protected $requiredAuth = true;

		function init(){
			if( $this->requiredAuth ){
				if( !defined( 'API_USERS' ) ){
					$this->showError( self::HTTP_STATUS_INTERNAL_ERROR, 'API_USERS_NOT_DEFINED' );
				}

				if( !isset( $_SERVER['PHP_AUTH_USER'] ) || !isset( $_SERVER['PHP_AUTH_PW'] ) ){
					$this->showError( self::HTTP_STATUS_UNAUTHORIZED, 'UNAUTHORIZED' );
				}

				if( !isset( API_USERS[ $_SERVER['PHP_AUTH_USER'] ] ) ){
					$this->showError( self::HTTP_STATUS_FORBIDDEN, 'FORBIDDEN' );
				}

				if( API_USERS[ $_SERVER['PHP_AUTH_USER'] ] != $_SERVER['PHP_AUTH_PW'] ){
					$this->showError( self::HTTP_STATUS_FORBIDDEN, 'FORBIDDEN' );
				}
			}
		}
	}
?>