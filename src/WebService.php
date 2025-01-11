<?php
	/**
	 * @copyright Business Computing Research Laboratory <www.bucorel.com>
	 * @author Pushpendra Singh Thakur <thakurpsr@gmail.com>
	 */

	namespace Bucorel\Waf;
	
	class WebService extends Router{

		function start(){
			$route = $this->getRoute();

			if( !defined( 'ROUTES' )){
				self::showHttpError( JsonResponse::HTTP_STATUS_INTERNAL_ERROR, 'ROUTES_NOT_DEFINED', 1 );
			}

			if( !isset( ROUTES[ $route ] ) ){
				self::showHttpError( JsonResponse::HTTP_STATUS_NOT_FOUND, 'UNDEFINED_ROUTE', 1 );
			}

			$controllerClass = ROUTES[ $route ];
			if( !class_exists( $controllerClass )){
				self::showHttpError( JsonResponse::HTTP_STATUS_INTERNAL_ERROR, 'UNDEFINED_CONTROLLER', 1 );
			}

			$controller = new $controllerClass();
			$controller->init();
			$controller->handleRequest();
		}
	}
?>