<?php
namespace Bucorel\Waf\Core;

/**
 * WebApp (Web Application) Router implementation.
 * * This class extends the base Router and provides specific dispatch logic
 * suitable for web applications:
 * 1. Checks for and applies dynamic per-route rate limits.
 * 2. Initializes session handling for stateful interactions.
 */
class WebService extends Router{

	protected $authList = [];

	function setAuthList( array $authList ){
		$this->authList = $authList;
	}

    /**
     * Dispatches the request through the application workflow.
     * * The workflow is: Resolve Route -> Apply Rate Limit -> Init Session -> Load Controller.
     * * @throws \ErrorException If the resolved route is undefined or the controller is missing.
     * @return void
     */
	function dispatch(): void{
		$route = $this->getRoute();

		//if no route is given in the REQUEST_URI use default route if configured
		if( $route == '' ){
			$route = $this->config['DEFAULT_ROUTE'] ?? '';
		}

		//if still no route
		if( $route == '' ){
			JsonResponse::showFatalError( 'NO_ROUTE_TO_DISPATCH' );
		}

		$controllerClassName = $this->getController( $route );

		/**
		 * @todo add request rate limitter here
		 */
		
		$this->loadController( $controllerClassName );
	}

	function run():void{
		$this->authCheck();
		$this->dispatch();
	}

	function authCheck(){
		$authMode = $this->config['AUTH_MODE'] ?? 'basic';
		switch( $authMode ){
			case 'basic':
				$this->basicAuthCheck();
				break;
			default:
				JsonResponse::showFatalError( 'UNAUTHORIZED', array(), JsonResponse::STATUS_UNAUTHORIZED );
		}
	}

	function basicAuthCheck(){
		if( empty( $_SERVER[ 'PHP_AUTH_USER'] ) || empty( $_SERVER[ 'PHP_AUTH_PW'] ) ){
			JsonResponse::showFatalError( 'UNAUTHORIZED', array(), JsonResponse::STATUS_UNAUTHORIZED );
		}

		if( !array_key_exists( $_SERVER['PHP_AUTH_USER'], $this->authList ) ){
			JsonResponse::showFatalError( 'UNAUTHORIZED', array(), JsonResponse::STATUS_UNAUTHORIZED );
		}

		if( $this->authList[ $_SERVER['PHP_AUTH_USER'] ] != $_SERVER[ 'PHP_AUTH_PW'] ){
			JsonResponse::showFatalError( 'UNAUTHORIZED', array(), JsonResponse::STATUS_UNAUTHORIZED );
		}
	}
}
?>