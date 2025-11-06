<?php
/** * Author - Pushpendra Singh Thakur <thakur@bucorel.com>
 * Copyright - Business Computing Research Laboratory <www.bucorel.com>
 */

namespace Bucorel\Waf\Core;

/**
 * The Router class handles incoming HTTP requests, determines the correct route,
 * and dispatches the corresponding controller for execution.
 */
abstract class Router{
    
    /**
     * Application operating mode, typically 'prod' or 'debug'.
     * Used for environment-specific logic (e.g., error reporting).
     * @var string
     */
    public $appMode = 'prod';

    /**
     * The base URL path (e.g., '/', '/my-app/'). Used to strip the subfolder 
     * from the request URI to get the relative route.
     * @var string
     */
    public $baseUrl = '/';

    /**
     * An associative array of defined routes where the key is the route string 
     * and the value is the fully qualified controller class name.
     * @var array<string, string>
     */
    public $routes = [];
    
    /**
     * An associative array of defined configuration where the key is the param string 
     * and the value is its value.
     * @var array<string, string>
     */
    public $config = [];

    /**
     * Router constructor.
     * Initializes the router with application configuration and registered routes.
     * * @param array $config Application configuration array (must contain APP_MODE and BASE_URL).
     * @param array $routes Registered routes array.
     */
    function __construct( array $config, array $routes = array() ){
        $this->appMode = $config['APP_MODE'] ?? 'prod';
        $this->baseUrl = $config['BASE_URL'] ?? '/';
        $this->routes = $routes;
		$this->config = $config;
    }
    
    /**
     * Dispatches the request.
     * This is the main entry point for routing: it finds the route, looks up the 
     * controller, and executes the controller logic.
     * * @return void
     */
	abstract function dispatch():void;
	/*
    function dispatch():void{
        $route = $this->getRoute();
        // Allow the route '' (homepage) to be dispatched if defined
        $controller = $this->getController( $route );

        if( !$controller && $route !== '' ){
            // Handle 404 error here if no controller found for a non-empty route
            // For now, let it proceed to loadController which will throw if false
            // This behavior needs robust error handling in a production scenario.
            // But we follow the original logic here.
        }

        $this->loadController( $controller );
    }
    */

    /**
     * Determines the requested route string from the server URI.
     * It sanitizes the URI by removing the base URL and any trailing slashes.
     * * @return string The clean, relative route (e.g., 'about', 'users/profile').
     */
    function getRoute():string{
        $uri = parse_url( $_SERVER[ 'REQUEST_URI' ], PHP_URL_PATH );

        // If your app is in a subfolder (like /pdp/)
        $route = substr( $uri, strlen( $this->baseUrl ) );

        // Remove any trailing slash
        $route = trim( $route, '/' );
        
        return $route;
    }
    
    /**
     * Retrieves the controller class name associated with the given route.
     * * @param string $route The sanitized route string.
     * @return string|false The fully qualified controller class name, or false if the route is not defined.
     */
    function getController( string $route ):string|false{
        if( array_key_exists( $route, $this->routes ) ){
            return $this->routes[ $route ];
        }
        
        return false;
    }

    /**
     * Instantiates and executes the specified controller.
     * Checks for the existence of the class and required methods (init and handleRequest).
     * * @param string $controllerClassName The fully qualified class name of the controller.
     * @return void
     * @throws \ErrorException If the controller class is not defined ('UNDEFINED_CONTROLLER').
     * @throws \ErrorException If the controller is missing the required 'init' or 'handleRequest' methods ('INVALID_CONTROLLER').
     */
    function loadController( string $controllerClassName ):void{
        // Explicitly check for false from getController which would indicate a 404 in dispatch
        if( $controllerClassName === false ){
            throw new \ErrorException( 'ROUTE_NOT_FOUND', 404 );
        }

        if( !class_exists( $controllerClassName ) ){
            throw new \ErrorException( 'UNDEFINED_CONTROLLER' );
        }
            
        $c = new $controllerClassName();
        
        if( !method_exists( $c, 'init' ) ){
            throw new \ErrorException( 'INVALID_CONTROLLER' );
        }
        
        $c->init( $this->config );

        if( !method_exists( $c, 'handleRequest' ) ){
            throw new \ErrorException( 'INVALID_CONTROLLER' );
        }

        $c->handleRequest();
    }

	/**
     * Initializes the PHP session and configures custom session handling.
     * * If configured, this method sets the session save handler and path 
     * (via ini_set) to enable distributed session storage (e.g., Redis, database).
     * It also sets a custom session cookie name before initiating the session.
     * * NOTE: This function MUST be called before any output is sent to the client.
     * @return void
     */
    function initSession(): void {
        // Best practice: Always check if a session is already active to prevent PHP warnings.
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $sessionName = $this->config['SESSION_NAME'] ?? null;
        $sessionSaveHandler = $this->config['SESSION_SAVE_HANDLER'] ?? null;
        $sessionSavePath = $this->config['SESSION_SAVE_PATH'] ?? null;

        // If custom session handler and path are defined, configure PHP to use them.
        if( $sessionSaveHandler && $sessionSavePath ){
            ini_set('session.save_handler', $sessionSaveHandler );
            ini_set('session.save_path', $sessionSavePath );
        }

        // If a custom session cookie name is defined, set it before starting the session.
        if( $sessionName ){
            session_name( $sessionName );
        }

        // Start the PHP session, enabling the use of the $_SESSION superglobal.
        session_start();
    }
}
?>
