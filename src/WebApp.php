<?php
	/**
	 * @copyright Business Computing Research Laboratory <www.bucorel.com>
	 * @author Pushpendra Singh Thakur <thakurpsr@gmail.com>
	 */

	namespace Bucorel\Waf;
	
	class WebApp extends Router{

		function start(){
			$route = $this->getRoute();

			if( !defined( 'ROUTES' )){
				self::showHttpError( JsonResponse::HTTP_STATUS_INTERNAL_ERROR, 'ROUTES_NOT_DEFINED' );
			}

			if( !isset( ROUTES[ $route ] ) ){
				if( $_SERVER[ 'REQUEST_METHOD' ] != 'GET' ){
					self::showHttpError( JsonResponse::HTTP_STATUS_NOT_FOUND, 'UNDEFINED_ROUTE' );
				}else{
					$route = DEFAULT_ROUTE;
				}
			}

			$controllerClass = ROUTES[ $route ];
			if( !class_exists( $controllerClass )){
				self::showHttpError( JsonResponse::HTTP_STATUS_INTERNAL_ERROR, 'UNDEFINED_CONTROLLER' );
			}

			$controller = new $controllerClass();
			$controller->init();
			$controller->handleRequest();
		}
	}
?>