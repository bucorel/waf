<?php
namespace Bucorel\Waf\Core;

/**
 * Abstract BaseController class.
 * All application-specific controllers must extend this class. It provides the core
 * request dispatch logic based on the HTTP method and leverages JsonResponse for
 * structured output and error handling.
 * * @abstract
 */
abstract class BaseController extends JsonResponse{

    protected $config = [];
    
    /**
     * Initializes the controller instance.
     * This abstract method must be implemented by all derived controllers to
     * perform necessary setup (e.g., dependency injection, variable assignment)
     * before handling the request.
     * * @abstract
     * @return void
     */
    abstract function init( array $config );

    /**
     * Main request handling dispatcher.
     * Checks the $_SERVER['REQUEST_METHOD'] and calls the corresponding
     * specialized handler method (handleGetRequest, handlePostRequest, etc.).
     * * @return void
     */
    function handleRequest(): void{
        // Use a strict type check switch for reliability
        switch( $_SERVER['REQUEST_METHOD'] ){
            case 'GET':
                $this->handleGetRequest();
                break;
            case 'POST':
                $this->handlePostRequest();
                break;
            case 'PUT':
                $this->handlePutRequest();
                break;
            case 'DELETE':
                $this->handleDeleteRequest();
                break;
            default:
                // For any other non-standard method
                $this->showUnsupportedMethodError();
        }
    }

    /**
     * Handler for HTTP GET requests.
     * Controllers should override this method to implement GET logic.
     * By default, it signals that the method is not implemented for the current resource.
     * * @return void
     */
    function handleGetRequest(): void{
        $this->showUnsupportedMethodError();
    }

    /**
     * Handler for HTTP POST requests (e.g., resource creation).
     * Controllers should override this method to implement POST logic.
     * By default, it signals that the method is not implemented for the current resource.
     * * @return void
     */
    function handlePostRequest(): void{
        $this->showUnsupportedMethodError();
    }

    /**
     * Handler for HTTP PUT requests (e.g., full resource replacement/update).
     * Controllers should override this method to implement PUT logic.
     * By default, it signals that the method is not implemented for the current resource.
     * * @return void
     */
    function handlePutRequest(): void{
        $this->showUnsupportedMethodError();
    }

    /**
     * Handler for HTTP DELETE requests (e.g., resource deletion).
     * Controllers should override this method to implement DELETE logic.
     * By default, it signals that the method is not implemented for the current resource.
     * * @return void
     */
    function handleDeleteRequest(): void{
        $this->showUnsupportedMethodError();
    }

    /**
     * Sets the status code to 405 (Method Not Allowed) and sends the response.
     * This is the default error response for any request method that hasn't been
     * explicitly overridden in the concrete controller.
     * * @return void
     */
    function showUnsupportedMethodError(): void{
        // Assuming constants are available in JsonResponse or globally
        $this->setStatus( self::STATUS_METHOD_NOT_ALLOWED ); // Assuming STATUS_METHOD_NOT_ALLOWED is 405
        $this->setMessage( 'ERROR_UNSUPPORTED_METHOD' );
        $this->finish(); // Sends the response
    }
}
