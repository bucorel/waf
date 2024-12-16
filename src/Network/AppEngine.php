<?php
	/**
	 * @copyright Business Computing Research Laboratory <www.bucorel.com>
	 * @author Pushpendra Singh Thakur
	 */

	namespace Bucorel\Waf\Network;
	
	class AppEngine{
		
		function start() : void {
			$route = $this->getRoute();

			if( !$controller = $this->getController( $route ) ){
				self::showHttpError( 404, 'UNKNOWN_ROUTE' );
			}
			
			if( !class_exists( $controller ) ){
				self::showHttpError( 500, 'BAD_CONTROLLER' );
			}
			
			$handler = new $controller();
			$handler->init();
			$handler->handleRequest();
		}
		
		function getRoute() : string {
			if( !isset( $_REQUEST[ '_route' ] ) || $_REQUEST[ '_route' ] == '' ){
				return DEFAULT_ROUTE;
			}else{
				return $_REQUEST[ '_route' ];
			}
		}
		
		function getController( string $route ) : string|false {
			if( isset( ROUTES[ $route ] ) ){
				return ROUTES[ $route ];
			}
			
			return false;
		}
		
		public static function showHttpError( int $statusCode, string $message ) : void {
			$output = array(
				'_sta'=>0,
				'_mes'=>$message
			);
			
			http_response_code( $statusCode );
			header( 'Content-Type:application/json' );
			echo json_encode( $output );
			exit;
		} 
	}
?>
