<?php
namespace Bucorel\Waf\Core;

/**
 * Class JsonResponse
 * * Manages the creation, formatting, and delivery of standardized JSON API responses.
 * This includes setting HTTP status codes, CORS headers, response data, and metadata.
 */
class JsonResponse{
	
	// --- HTTP Status Codes ---
	
	/** @var int Successful request - OK */
	const STATUS_OK = 200;
	/** @var int Resource successfully created */
	const STATUS_CREATED = 201;
	/** @var int Request has been accepted for processing */
	const STATUS_ACCEPTED = 202;
	
	/** @var int Client error - Bad Request (e.g., malformed syntax) */
	const STATUS_BAD_REQUEST = 400;
	/** @var int Authentication failed */
	const STATUS_UNAUTHORIZED = 401;
	/** @var int Client does not have access rights to the content */
	const STATUS_FORBIDDEN = 403;
	/** @var int The requested resource was not found */
	const STATUS_NOT_FOUND = 404;
	/** @var int Request method is not supported for the resource */
	const STATUS_METHOD_NOT_ALLOWED = 405;
	/** @var int Conflict (e.g., resource already exists) */
	const STATUS_CONFLICT = 409;
	/** @var int Server error - Internal Server Error */
	const STATUS_INTERNAL_ERROR = 500;

	// --- Response Envelope Keys ---
	// (Note: Using short acronyms for internal consistency)
	
	/** @var string Key for the HTTP status code in the response envelope. */
	const STATUS_KEY = '_sta';
	/** @var string Key for the human-readable message in the response envelope. */
	const MESSAGE_KEY = '_mes';
	/** @var string Key for the main data payload in the response envelope. */
	const DATA_KEY = '_dat';
	/** @var string Key for the request execution time (elapsed time) in the response envelope. */
	const ELAPSED_TIME_KEY = '_et';
	
	/** @var array List of allowed domain origins for CORS. */
	protected $allowedOrigins = [];
	/** @var array The final associative array to be encoded as JSON. */
	protected $response = [];
	
	/**
	 * Sets the list of allowed origins for Cross-Origin Resource Sharing (CORS).
	 *
	 * @param array $originList Array of origin strings (e.g., ['https://example.com']).
	 * @return void
	 */
	function setAllowedOrigins( array $originList ): void{
		$this->allowedOrigins = $originList;
	}
	
	/**
	 * Sets the HTTP status code for the response.
	 *
	 * @param int $status The HTTP status code (e.g., 200, 404).
	 * @return void
	 */
	function setStatus( int $status=self::STATUS_OK ) : void{
		$this->response[ self::STATUS_KEY ] = $status;
	}
	
	/**
	 * Sets the human-readable message for the response.
	 *
	 * @param string $message The descriptive message (e.g., "OK", "User not found").
	 * @return void
	 */
	function setMessage( string $message ): void{
		$this->response[ self::MESSAGE_KEY ] = $message;
	}
	
	/**
	 * Sets the main data payload of the response.
	 *
	 * @param array $data The resource data (e.g., an array of users, a single object).
	 * @return void
	 */
	function setData( array $data ): void{
		$this->response[ self::DATA_KEY ] = $data;
	}
	
	/**
	 * Appends an item to the data array. Initializes the data key if not set.
	 *
	 * @param array $data The item to append.
	 * @return void
	 */
	function appendData( array $data ): void{
		if( !isset( $this->response[ self::DATA_KEY ] ) || !is_array( $this->response[ self::DATA_KEY ] ) ){
			$this->response[ self::DATA_KEY ] = [];
		}
		
		// Use array assignment for better performance than array_push
		$this->response[ self::DATA_KEY ][] = $data; 
	}
	
	/**
	 * Adds a custom key/value pair directly to the root of the JSON response (metadata).
	 *
	 * @param string $key The custom key name.
	 * @param mixed $value The value for the custom key.
	 * @return void
	 */
	function addMeta( string $key, $value ): void{
		$this->response[ $key ] = $value;
	}
		
	/**
	 * Finalizes the response: sets headers, calculates execution time, echoes JSON, and terminates script execution.
	 *
	 * @return void
	 */
	function finish():void{
		// Set default status to 200 OK if none was explicitly set
		if( !isset( $this->response[ self::STATUS_KEY ] ) ){
			$this->response[ self::STATUS_KEY ] = self::STATUS_OK;
		}
		
		// Set the actual HTTP response code
		http_response_code( $this->response[ self::STATUS_KEY ] );
		
		// Handle CORS headers
		$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
		if ( in_array( $origin, $this->allowedOrigins ) ) {
			header('Access-Control-Allow-Origin: '.$origin );
			header('Access-Control-Allow-Credentials: true');
			header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
			header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
		}
		
		// Set Content-Type header
		header('Content-Type: application/json; charset=utf-8');
		
		// Add execution time to the response metadata
		$this->response[ self::ELAPSED_TIME_KEY ] = $this->getElapsedTime();
		
		// Encode and output the JSON response
		echo json_encode( $this->response, JSON_UNESCAPED_UNICODE );
		exit;
	}
	
	/**
	 * Calculates the elapsed time from the start of the request.
	 *
	 * @return float The duration in seconds.
	 */
	function getElapsedTime():float{
		$end = microtime( true );
		// $_SERVER['REQUEST_TIME_FLOAT'] contains the request start time
		$start = $_SERVER['REQUEST_TIME_FLOAT']; 
		return $end - $start;
	}
	
	/**
	 * Utility method to quickly send a simple message response (e.g., success or error notices).
	 *
	 * @param string $message The descriptive message.
	 * @param int $status The HTTP status code (defaults to 200).
	 * @param array $meta Additional metadata to merge into the response root.
	 * @return void
	 */
	function showMessage( string $message, int $status = self::STATUS_OK, array $meta = array() ):void{
		$this->setStatus( $status );
		$this->setMessage( $message );
		
		// Merge additional metadata keys into the response
		$this->response = array_merge($this->response, $meta); 
		
		$this->finish();
	}
	
	/**
	 * Utility method to quickly send a standard successful API data output (200 OK).
	 *
	 * @param array $data The main data payload.
	 * @return void
	 */
	function sendData( array $data ):void{
		$this->setStatus( self::STATUS_OK );
		$this->setData( $data );
		$this->finish();
	}

	public static function showFatalError( string $message, array $debugInfo=array(), int $httpStatusCode = 500 ):void{
		$jr = new JsonResponse();
		$jr->setStatus( $httpStatusCode );
		$jr->setMessage( $message );
		$jr->addMeta( '_debug', $debugInfo );
		$jr->finish();
	}
}
