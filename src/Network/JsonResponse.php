<?php
	/**
	 * @copyright Business Computing Research Laboratory <www.bucorel.com>
	 * @author Pushpendra Singh Thakur <thakurpsr@gmail.com>
	 */
	
	namespace Bucorel\Waf\Network;
	
	class JsonResponse{
		
		
		const STATUS_OK = 200;
		const STATUS_CREATED = 201;
		const STATUS_ACCEPTED = 202;
		
		const STATUS_BAD_REQUEST = 400;
		const STATUS_UNAUTHORIZED = 401;
		const STATUS_NOT_FOUND = 404;
		const STATUS_METHOD_NOT_ALLOWED = 405;
		const STATUS_CONFLICT = 409;
		
		const STATUS_INTERNAL_ERROR = 500;
		
		
		protected $output = array();
		
		
		function finish( int $httpStatus = 200 ) : void {
			$this->setResponseStatus( $httpStatus );
			
			http_response_code( $httpStatus );
			header( 'Content-Type:application/json' );
			
			$this->output['_tts'] = number_format( (microtime( true ) - $_SERVER['REQUEST_TIME_FLOAT']),3);
			
			echo json_encode( $this->output );
			exit;
		}
		
		
		function setResponseStatus( int $httpStatus ) : void {
			if( in_array( $httpStatus, array( 200, 201, 202 ) ) ){
				$this->output['_sta'] = 1;
			}else{
				$this->output['_sta'] = 0;
			}
		}
		
		
		function showOk( string $message ) : void{
			$this->output['_mes'] = $message;
			
			$this->finish();
		}
		
		
		function showError( int $httpStatus, string $message, string $fieldName="" ) : void{
			$this->output['_mes'] = $message;
			if( $fieldName != '' ){
				$this->output['_fie'] = $fieldName;
			}
			
			$this->finish( $httpStatus );
		}
		
		
		function showData( array $data ) : void {
			$this->output['_dat'] = $data;
			
			$this->finish();
		}
		
		function appendData( array $data ){
			if( !isset( $this->output['_dat'] ) ){
				$this->output['_dat'] = array();
			}
			
			array_push( $this->output['_dat'], $data );
		}
	}
?>
