<?php
	namespace Bucorel\Waf;

	class Router{

		function getRoute() : string {
			if( isset( $_REQUEST['_route'] ) && $_REQUEST['_route'] != '' ){
				return $_REQUEST['_route'];
			}else{
				return DEFAULT_ROUTE;
			}
		}

		public static function showHttpError( int $httpStatusCode, string $message, $isService=0 ) : void {
			if( $_SERVER['REQUEST_METHOD'] != 'GET' || $isService == 1 ){
				$jr = new JsonResponse();
				$jr->showError( $httpStatusCode, $message );
			}else{
				http_response_code( $httpStatusCode );
				echo $message;
				exit;
			}
		}
	}
?>